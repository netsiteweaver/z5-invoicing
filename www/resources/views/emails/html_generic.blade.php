<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica, Arial, sans-serif; color:#111827; }
        .container { max-width: 640px; margin: 0 auto; padding: 16px; }
        .card { background:#ffffff; border:1px solid #e5e7eb; border-radius:8px; padding:16px; }
        .footer { color:#6b7280; font-size:12px; margin-top:16px; }
    </style>
    <!-- Inline styles for broad email client support -->
</head>
<body>
    <div class="container">
        <div class="card">
            {!! $bodyHtml !!}
        </div>
        <div class="footer">
            Sent by {{ config('app.name') }}
        </div>
    </div>
</body>
</html>


