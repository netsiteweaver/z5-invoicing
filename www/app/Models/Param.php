<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Param extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
        ];
    }

    public static function getValue(string $key, ?string $default = null): ?string
    {
        $param = static::query()->where('key', $key)->first();
        return $param?->value ?? $default;
    }

    public static function setValue(string $key, ?string $value, ?array $meta = null): void
    {
        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'meta' => $meta]
        );
    }

    public static function incrementAndGet(string $key, int $initial = 0): int
    {
        return DB::transaction(function () use ($key, $initial) {
            $param = static::query()->lockForUpdate()->where('key', $key)->first();
            if (!$param) {
                $param = new static(['key' => $key, 'value' => (string) $initial]);
                $param->save();
            }
            $current = (int) ($param->value ?? 0);
            $next = $current + 1;
            $param->value = (string) $next;
            $param->save();
            return $next;
        });
    }
}
