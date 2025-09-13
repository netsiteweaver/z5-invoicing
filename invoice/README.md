Invoice Generator (HTML)
========================

Generate a professional, print-friendly HTML invoice from JSON data using Jinja2.

Quick start
-----------

1) Install dependencies

```bash
python3 -m venv .venv && source .venv/bin/activate
pip install -r requirements.txt
```

2) Render the sample invoice

```bash
python generate_invoice.py --data data.sample.json
```

The HTML will be written to `output/invoice_<number>.html`. Open it in your browser and print to PDF if you like.

Data format
-----------

See `data.sample.json` for a complete example. Key sections:

- `company`: Your business info (name, email, address, initials)
- `client`: Recipient info
- `invoice`: `{ number, date, due_date }`
- `currency`: `{ code, symbol }`
- `items`: Array of `{ description, note?, quantity, unit_price }`
- `totals`: Can include any of:
  - `discount`: absolute amount
  - `discount_percent`: percentage (e.g. 0.1 for 10%)
  - `shipping`: absolute amount
  - `tax`: absolute amount
  - `tax_rate`: percentage (applied to subtotal - discount)
- `notes`, `terms`, `payment`, `webfonts`

Only `items` are required; the generator computes `subtotal`, `discount`, `tax`, `shipping`, and `total` for the template.

Optional PDF
------------

The script can try to produce a PDF if you pass `--out-pdf path.pdf`. It will attempt:

1. `wkhtmltopdf` if found on your system
2. `weasyprint` if installed (`pip install weasyprint` and system deps)
3. `pdfkit` if installed and configured

If none are available, open the HTML in a browser and print to PDF.

Customizing the template
------------------------

Edit `templates/invoice.html.j2` to adjust branding, colors, or layout. Pass `webfonts: true` to use Inter via Google Fonts.

