<html>
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
    .header { text-align: center; margin-bottom: 10px; }
    .title { font-size: 18px; font-weight: bold; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #444; padding: 6px; }
    th { background: #f0f0f0; }
  </style>
</head>
<body>
  <div class="header">
    <div class="title">Goods Receipt Note</div>
    <div>GRN: {{ $receipt->grn_number }} | Date: {{ $receipt->receipt_date->format('Y-m-d') }}</div>
    <div>Location: {{ $receipt->department->name ?? '-' }}</div>
    <div>Supplier: {{ $receipt->supplier->name ?? ($receipt->supplier_name ?? '-') }}</div>
  </div>

  <table>
    <thead>
      <tr>
        <th style="width: 40%">Product</th>
        <th style="width: 15%">Qty</th>
        <th style="width: 20%">Unit Cost</th>
        <th style="width: 25%">Line Total (incl. VAT)</th>
      </tr>
    </thead>
    <tbody>
      @php
        $totalQty = 0;
        $totalNet = 0;
        $totalVat = 0;
        $totalGross = 0;
      @endphp
      @foreach($receipt->items as $item)
        @php
          $unitCost = $item->unit_cost ?? 0;
          $quantity = $item->quantity;
          $grossAmount = $quantity * $unitCost;
          $vatRate = ($item->product->tax_type ?? 'standard') === 'standard' ? 0.15 : 0;
          $vatAmount = $grossAmount * $vatRate;
          $lineTotal = $grossAmount + $vatAmount;
          
          $totalQty += $quantity;
          $totalNet += $grossAmount;
          $totalVat += $vatAmount;
          $totalGross += $lineTotal;
        @endphp
      <tr>
        <td>{{ $item->product->name ?? ('#'.$item->product_id) }}</td>
        <td>{{ $quantity }}</td>
        <td>{{ number_format($unitCost, 2) }}</td>
        <td>{{ number_format($lineTotal, 2) }}</td>
      </tr>
      @endforeach
    </tbody>
    <tfoot>
      <tr style="background: #f0f0f0; font-weight: bold;">
        <td>Totals:</td>
        <td>{{ $totalQty }}</td>
        <td>Products (excl. VAT): {{ number_format($totalNet, 2) }}</td>
        <td>
          <div>VAT (15%): {{ number_format($totalVat, 2) }}</div>
          <div>Grand Total: {{ number_format($totalGross, 2) }}</div>
        </td>
      </tr>
    </tfoot>
  </table>

  @if($receipt->notes)
  <p><strong>Notes:</strong> {{ $receipt->notes }}</p>
  @endif
</body>
</html>
