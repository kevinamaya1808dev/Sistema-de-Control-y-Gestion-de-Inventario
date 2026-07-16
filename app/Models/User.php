<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    /**
     * Relación con el rol del usuario.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Relación de muchos a muchos con los permisos personalizados.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_user');
    }

    /**
     * Comprobar si el usuario tiene un permiso específico por su slug.
     */
    public function hasPermissionTo(string $permissionSlug): bool
    {
        return $this->permissions->contains('slug', $permissionSlug);
    }

    /**
     * Comprobar de forma rápida si el usuario tiene el rol de Administrador.
     */
    public function isAdmin(): bool
    {
        return $this->role && $this->role->name === 'Administrador';
    }
}