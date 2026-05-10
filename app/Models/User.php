<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_SUPERADMIN = 'superadmin';
    public const ROLE_USER = 'user';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'bio',
        'password',
        'role',
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
        ];
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function likesReceived(): HasManyThrough
    {
        return $this->hasManyThrough(Like::class, Post::class);
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    public function bookmarkedPosts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'bookmarks')
            ->withTimestamps();
    }

    public function subscribedUsers(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'user_subscriptions',
            'subscriber_id',
            'subscribed_user_id'
        )->withTimestamps();
    }

    public function subscribers(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'user_subscriptions',
            'subscribed_user_id',
            'subscriber_id'
        )->withTimestamps();
    }

    /**
     * @return list<string>
     */
    public static function roles(): array
    {
        return [
            self::ROLE_SUPERADMIN,
            self::ROLE_USER,
        ];
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPERADMIN;
    }

    public function isSimpleUser(): bool
    {
        return $this->role === self::ROLE_USER;
    }
}
