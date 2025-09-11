<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'name',
        'display_name',
        'description',
        'module',
        'action',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
        });
    }

    // Relationships
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByModule($query, $module)
    {
        return $query->where('module', $module);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    // Static methods for permission checking
    public static function getModulePermissions($module)
    {
        return static::active()->byModule($module)->get();
    }

    public static function getActionPermissions($action)
    {
        return static::active()->byAction($action)->get();
    }

    // Permission name generation helpers
    public static function generatePermissionName($module, $action)
    {
        return "{$module}.{$action}";
    }

    public static function generateDisplayName($module, $action)
    {
        return ucfirst($action) . ' ' . ucfirst(str_replace('_', ' ', $module));
    }
}