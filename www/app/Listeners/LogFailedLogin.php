<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use Illuminate\Http\Request;
use App\Models\LoginActivity;
use App\Services\GeoIpService;

class LogFailedLogin
{
    public function __construct(private Request $request, private GeoIpService $geoIp)
    {
    }

    public function handle(Failed $event): void
    {
        $email = is_array($event->credentials) ? ($event->credentials['email'] ?? null) : null;
        $this->store('failed', $event->user?->id, $email);
    }

    private function store(string $status, ?int $userId, ?string $email): void
    {
        $agent = $this->request->header('User-Agent', '');
        [$device, $os, $browser] = $this->parseUserAgent($agent);

        $geo = $this->geoIp->lookup($this->request->ip());
        LoginActivity::create([
            'user_id' => $userId,
            'email' => $email,
            'status' => $status,
            'ip_address' => $this->request->ip(),
            'country' => $geo['country'] ?? null,
            'city' => $geo['city'] ?? null,
            'device' => $device,
            'os' => $os,
            'browser' => $browser,
            'user_agent' => $agent,
        ]);
    }

    private function parseUserAgent(string $ua): array
    {
        $device = str_contains($ua, 'Mobile') ? 'Mobile' : 'Desktop';
        $os = (preg_match('/Windows|Mac OS X|Linux|Android|iPhone OS/i', $ua, $m)) ? $m[0] : 'Unknown';
        $browser = (preg_match('/Chrome|Firefox|Safari|Edge|OPR|Opera|MSIE|Trident/i', $ua, $m)) ? $m[0] : 'Unknown';
        if ($browser === 'OPR') { $browser = 'Opera'; }
        if ($browser === 'Trident') { $browser = 'IE'; }
        return [$device, $os, $browser];
    }
}


