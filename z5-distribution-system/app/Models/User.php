<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'uuid',
        'name',
        'email',
        'username',
        'password',
        'user_level',
        'job_title',
        'photo',
        'store_id',
        'landing_page',
        'last_login',
        'ip',
        'token',
        'token_valid_until',
        'created_by',
        'updated_by',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login' => 'datetime',
            'token_valid_until' => 'datetime',
            'status' => 'integer',
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
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class, 'manager_id');
    }

    public function createdCustomers(): HasMany
    {
        return $this->hasMany(Customer::class, 'created_by');
    }

    public function createdProducts(): HasMany
    {
        return $this->hasMany(Product::class, 'created_by');
    }

    public function createdOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'created_by');
    }

    public function createdSales(): HasMany
    {
        return $this->hasMany(Sale::class, 'created_by');
    }

    public function createdPayments(): HasMany
    {
        return $this->hasMany(Payment::class, 'created_by');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    // Permission checking methods
    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->roles()->where('name', $role)->exists();
        }
        
        return $this->roles()->where('role_id', $role->id)->exists();
    }

    public function hasPermission($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->first();
        }

        if (!$permission) {
            return false;
        }

        return $this->roles()->whereHas('permissions', function ($query) use ($permission) {
            $query->where('permission_id', $permission->id);
        })->exists();
    }

    public function hasAnyPermission($permissions)
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    public function hasAllPermissions($permissions)
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }

    public function assignRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->first();
        }

        if ($role && !$this->hasRole($role)) {
            $this->roles()->attach($role->id);
        }

        return $this;
    }

    public function removeRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->first();
        }

        if ($role) {
            $this->roles()->detach($role->id);
        }

        return $this;
    }

    public function syncRoles($roles)
    {
        $roleIds = collect($roles)->map(function ($role) {
            if (is_string($role)) {
                $role = Role::where('name', $role)->first();
            }
            return $role ? $role->id : null;
        })->filter()->toArray();

        $this->roles()->sync($roleIds);
        return $this;
    }

    public function getAllPermissions()
    {
        return $this->roles()->with('permissions')->get()
            ->pluck('permissions')
            ->flatten()
            ->unique('id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('user_level', $level);
    }

    // Accessors & Mutators
    public function getIsAdminAttribute(): bool
    {
        return in_array($this->user_level, ['Admin', 'Root']);
    }

    public function getIsRootAttribute(): bool
    {
        return $this->user_level === 'Root';
    }
}
