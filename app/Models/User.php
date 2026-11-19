<?php

namespace App\Models;

use App\Enums\Role;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'city',
        'avatar_color',
        'rating',
        'reviews_count',
        'bio',
        'joined_at',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
            'joined_at' => 'date',
            'rating' => 'decimal:1',
        ];
    }

    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class, 'seller_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function driverAssignments(): HasMany
    {
        return $this->hasMany(Order::class, 'driver_id');
    }

    public function threadsAsBuyer(): HasMany
    {
        return $this->hasMany(ChatThread::class, 'buyer_id');
    }

    public function threadsAsSeller(): HasMany
    {
        return $this->hasMany(ChatThread::class, 'seller_id');
    }

    public function reviewsReceived(): HasMany
    {
        return $this->hasMany(Review::class, 'seller_id');
    }

    public function reviewsWritten(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    /**
     * Sales are order items whose seller is this user.
     */
    public function sales(): HasManyThrough
    {
        return $this->hasManyThrough(OrderItem::class, Order::class, 'user_id', 'order_id')
            ->whereColumn('order_items.seller_id', 'users.id');
    }

    public function isAdmin(): bool
    {
        return $this->role === Role::Admin;
    }

    public function isDriver(): bool
    {
        return $this->role === Role::Driver;
    }

    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->filter()
            ->take(2)
            ->map(fn (string $part): string => Str::upper(Str::substr($part, 0, 1)))
            ->implode('');
    }

    #[Scope]
    protected function drivers(Builder $query): Builder
    {
        return $query->where('role', Role::Driver);
    }
}
