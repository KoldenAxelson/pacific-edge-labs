<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * User model representing system users in the Pacific Edge Labs application.
 *
 * Manages user authentication, role-based access control, and compliance tracking.
 * Users can have roles such as super-admin, admin, manager, or customer, which determine
 * their access to the Filament admin panel and system features. Integrates with Laravel's
 * authentication system and Spatie's permission package for role management.
 *
 * @see \App\Models\Order
 * @see \App\Models\PaymentTransaction
 */
class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ["name", "email", "password"];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ["password", "remember_token"];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            "email_verified_at" => "datetime",
            "password" => "hashed",
        ];
    }

    /**
     * Determine if the user can access the Filament admin panel.
     *
     * Only users with super-admin, admin, or manager roles are granted access.
     *
     * @param Panel $panel The Filament panel being accessed
     * @return bool True if the user has an admin-tier role
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasRole(["super-admin", "admin", "manager"]);
    }
}
