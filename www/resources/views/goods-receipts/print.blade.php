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
        <th style="width: 60%">Product</th>
        <th style="width: 20%">Qty</th>
        <th style="width: 20%">Unit Cost</th>
      </tr>
    </thead>
    <tbody>
      @foreach($receipt->items as $item)
      <tr>
        <td>{{ $item->product->name ?? ('#'.$item->product_id) }}</td>
        <td>{{ $item->quantity }}</td>
        <td>{{ $item->unit_cost ? number_format($item->unit_cost, 2) : '-' }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>

  @if($receipt->notes)
  <p><strong>Notes:</strong> {{ $receipt->notes }}</p>
  @endif
</body>
</html>
