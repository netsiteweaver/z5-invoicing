<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'name',
        'display_name',
        'description',
        'is_active',
        'is_system',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_system' => 'boolean',
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
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_roles');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    public function scopeNonSystem($query)
    {
        return $query->where('is_system', false);
    }

    // Permission management methods
    public function givePermissionTo($permission)
    {
        if (is_numeric($permission)) {
            $permissionId = (int) $permission;
        } elseif (is_string($permission)) {
            $permissionId = optional(Permission::where('name', $permission)->first())->id;
        } elseif ($permission instanceof Permission) {
            $permissionId = $permission->id;
        } else {
            $permissionId = null;
        }

        if ($permissionId && !$this->permissions()->where('permission_id', $permissionId)->exists()) {
            $this->permissions()->attach($permissionId);
        }

        return $this;
    }

    public function revokePermissionTo($permission)
    {
        if (is_numeric($permission)) {
            $permissionId = (int) $permission;
        } elseif (is_string($permission)) {
            $permissionId = optional(Permission::where('name', $permission)->first())->id;
        } elseif ($permission instanceof Permission) {
            $permissionId = $permission->id;
        } else {
            $permissionId = null;
        }

        if ($permissionId) {
            $this->permissions()->detach($permissionId);
        }

        return $this;
    }

    public function hasPermissionTo($permission)
    {
        if (is_numeric($permission)) {
            $permissionId = (int) $permission;
        } elseif (is_string($permission)) {
            $permissionId = optional(Permission::where('name', $permission)->first())->id;
        } elseif ($permission instanceof Permission) {
            $permissionId = $permission->id;
        } else {
            $permissionId = null;
        }

        return $permissionId ? $this->permissions()->where('permission_id', $permissionId)->exists() : false;
    }

    public function syncPermissions($permissions)
    {
        $permissionIds = collect($permissions)->map(function ($permission) {
            if (is_numeric($permission)) {
                return (int) $permission;
            }
            if (is_string($permission)) {
                return optional(Permission::where('name', $permission)->first())->id;
            }
            if ($permission instanceof Permission) {
                return $permission->id;
            }
            return null;
        })->filter()->unique()->toArray();

        $this->permissions()->sync($permissionIds);
        return $this;
    }

    // Check if role can be deleted
    public function canBeDeleted()
    {
        return !$this->is_system && $this->users()->count() === 0;
    }
}