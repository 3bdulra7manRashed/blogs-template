<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_admin',
        'is_super_admin',
        'short_bio',
        'biography',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_admin' => 'boolean',
            'is_super_admin' => 'boolean',
        ];
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(Media::class);
    }

    public function isAdmin(): bool
    {
        return $this->is_admin || $this->hasRole('admin') || $this->role === UserRole::ADMIN;
    }

    public function isModerator(): bool
    {
        return $this->hasRole('moderator') || $this->role === UserRole::EDITOR;
    }

    // Alias for backward compatibility
    public function isEditor(): bool
    {
        return $this->isModerator();
    }

    /**
     * Check if user is super admin (boolean column or super-admin role)
     * For Spatie roles: use hasRole('super-admin') instead
     */
    public function isSuperAdmin(): bool
    {
        // Option 1: Boolean column (default)
        if ($this->is_super_admin) {
            return true;
        }

        // Option 2: Spatie role (uncomment if using roles instead of boolean)
        // return $this->hasRole('super-admin');

        return false;
    }

    /**
     * Check if this user is the deleted user placeholder
     */
    public function isDeletedUserPlaceholder(): bool
    {
        $deletedUserEmail = config('app.deleted_user_email', env('DELETED_USER_EMAIL', 'deleted-user@local'));
        return $this->email === $deletedUserEmail;
    }
}
