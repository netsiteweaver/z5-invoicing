<p>Hello {{ $user->name }},</p>
<p>Your account has been created on {{ config('app.name') }}.</p>
<p>Username: {{ $user->username }}</p>
<p>You can now sign in at <a href="{{ url('/') }}">{{ url('/') }}</a>.</p>
<p>Thank you.</p>


