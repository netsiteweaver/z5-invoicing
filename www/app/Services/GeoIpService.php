<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GeoIpService
{
    public function lookup(?string $ip): array
    {
        if (!$ip || $ip === '127.0.0.1' || $ip === '::1') {
            return ['country' => null, 'city' => null];
        }

        $cacheKey = 'geoip:' . $ip;
        return Cache::remember($cacheKey, now()->addDays(7), function () use ($ip) {
            try {
                $resp = Http::timeout(2)->get('http://ip-api.com/json/' . $ip, [
                    'fields' => 'status,country,city',
                ]);
                if ($resp->successful() && ($resp['status'] ?? '') === 'success') {
                    return [
                        'country' => $resp['country'] ?? null,
                        'city' => $resp['city'] ?? null,
                    ];
                }
            } catch (\Throwable $e) {
                // ignore and fallthrough
            }
            return ['country' => null, 'city' => null];
        });
    }
}


