<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginActivity extends Model
{
    use HasFactory;

    protected $table = 'login_activities';
    public $timestamps = true;

    protected $fillable = [
        'user_id', 'email', 'status', 'ip_address', 'country', 'city',
        'device', 'os', 'browser', 'user_agent',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}


