#!/usr/bin/env python3
import argparse
import json
import sys
from pathlib import Path
from typing import Any, Dict
import shutil
import subprocess

from jinja2 import Environment, FileSystemLoader, select_autoescape


ROOT_DIR = Path(__file__).resolve().parent
TEMPLATES_DIR = ROOT_DIR / "templates"
OUTPUT_DIR = ROOT_DIR / "output"


def read_json_file(json_path: Path) -> Dict[str, Any]:
    with json_path.open("r", encoding="utf-8") as f:
        return json.load(f)


def compute_totals(data: Dict[str, Any]) -> Dict[str, float]:
    items = data.get("items", []) or []

    subtotal = 0.0
    for item in items:
        quantity = float(item.get("quantity", 0) or 0)
        unit_price = float(item.get("unit_price", 0) or 0)
        subtotal += quantity * unit_price

    totals_spec = data.get("totals", {}) or {}

    discount_value = totals_spec.get("discount")
    discount_percent = totals_spec.get("discount_percent")
    if discount_value is None and discount_percent is not None:
        try:
            discount = round(subtotal * float(discount_percent), 2)
        except Exception:
            discount = 0.0
    else:
        discount = float(discount_value or 0.0)

    shipping = float(totals_spec.get("shipping", 0.0) or 0.0)

    taxable_base = max(subtotal - discount, 0.0)
    tax_value = totals_spec.get("tax")
    tax_rate = totals_spec.get("tax_rate")
    if tax_value is None and tax_rate is not None:
        try:
            tax = round(taxable_base * float(tax_rate), 2)
        except Exception:
            tax = 0.0
    else:
        tax = float(tax_value or 0.0)

    total = subtotal - discount + tax + shipping

    return {
        "subtotal": round(subtotal, 2),
        "discount": round(discount, 2),
        "tax": round(tax, 2),
        "shipping": round(shipping, 2),
        "total": round(total, 2),
    }


def render_html(data: Dict[str, Any], output_html_path: Path) -> None:
    env = Environment(
        loader=FileSystemLoader(str(TEMPLATES_DIR)),
        autoescape=select_autoescape(["html", "xml"]),
        enable_async=False,
    )
    template = env.get_template("invoice.html.j2")

    computed_totals = compute_totals(data)

    context = {
        "company": data.get("company", {}),
        "client": data.get("client", {}),
        "invoice": data.get("invoice", {}),
        "currency": data.get("currency", {"code": "USD", "symbol": "$"}),
        "items": data.get("items", []),
        "totals": computed_totals,
        "notes": data.get("notes", ""),
        "terms": data.get("terms", ""),
        "payment": data.get("payment", {}),
        "webfonts": bool(data.get("webfonts", False)),
    }

    html = template.render(**context)

    output_html_path.parent.mkdir(parents=True, exist_ok=True)
    output_html_path.write_text(html, encoding="utf-8")


def try_render_pdf(html_path: Path, pdf_path: Path) -> bool:
    """Attempt to render a PDF using locally available tools.

    Preference order:
      1) wkhtmltopdf (system binary)
      2) weasyprint (if installed)
      3) pdfkit (if installed and wkhtmltopdf available)

    Returns True if a PDF was successfully created.
    """
    # 1) wkhtmltopdf system binary
    wkhtmltopdf_bin = shutil.which("wkhtmltopdf")
    if wkhtmltopdf_bin:
        try:
            subprocess.run([wkhtmltopdf_bin, "--quiet", str(html_path), str(pdf_path)], check=True)
            return True
        except Exception:
            pass

    # 2) weasyprint
    try:
        from weasyprint import HTML  # type: ignore

        HTML(filename=str(html_path)).write_pdf(str(pdf_path))
        return True
    except Exception:
        pass

    # 3) pdfkit (requires wkhtmltopdf, often as system binary)
    try:
        import pdfkit  # type: ignore

        pdfkit.from_file(str(html_path), str(pdf_path), options={"quiet": ""})
        return True
    except Exception:
        pass

    return False


def main(argv: list[str] | None = None) -> int:
    parser = argparse.ArgumentParser(description="Render an HTML invoice from JSON data.")
    parser.add_argument("--data", required=True, help="Path to JSON data file")
    parser.add_argument("--out-html", default=None, help="Path to output HTML file (default: output/invoice_<number>.html)")
    parser.add_argument("--out-pdf", default=None, help="Optional path to output PDF (requires tools)")

    args = parser.parse_args(argv)

    data_path = Path(args.data).resolve()
    if not data_path.exists():
        print(f"Data file not found: {data_path}", file=sys.stderr)
        return 1

    data = read_json_file(data_path)

    invoice_info = (data.get("invoice") or {})
    invoice_number = str(invoice_info.get("number") or "DRAFT").replace("/", "-").replace(" ", "_")

    default_html_out = OUTPUT_DIR / f"invoice_{invoice_number}.html"
    output_html_path = Path(args.out_html).resolve() if args.out_html else default_html_out

    render_html(data, output_html_path)
    print(f"HTML written to: {output_html_path}")

    if args.out_pdf:
        output_pdf_path = Path(args.out_pdf).resolve()
        ok = try_render_pdf(output_html_path, output_pdf_path)
        if ok:
            print(f"PDF written to: {output_pdf_path}")
        else:
            print("Unable to generate PDF automatically. Install 'wkhtmltopdf' or 'weasyprint' and retry.")

    return 0


if __name__ == "__main__":
    raise SystemExit(main())

