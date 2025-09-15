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
    <div class="title">Stock Transfer</div>
    <div>No: {{ $transfer->transfer_number }} | Date: {{ $transfer->transfer_date->format('Y-m-d') }}</div>
    <div>From: {{ $transfer->fromDepartment->name ?? '-' }} → To: {{ $transfer->toDepartment->name ?? '-' }}</div>
    <div>Status: {{ ucfirst(str_replace('_', ' ', $transfer->status)) }}</div>
  </div>

  <table>
    <thead>
      <tr>
        <th style="width: 70%">Product</th>
        <th style="width: 30%">Qty</th>
      </tr>
    </thead>
    <tbody>
      @foreach($transfer->items as $item)
      <tr>
        <td>{{ $item->product->name ?? ('#'.$item->product_id) }}</td>
        <td>{{ $item->quantity }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>

  @if($transfer->notes)
  <p><strong>Notes:</strong> {{ $transfer->notes }}</p>
  @endif
</body>
</html>


