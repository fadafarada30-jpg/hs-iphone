<?php

namespace App\Models;

use App\Enums\ListingCondition;
use App\Enums\ListingStatus;
use App\Enums\Warranty;
use Database\Factories\ListingFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Listing extends Model
{
    /** @use HasFactory<ListingFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'seller_id',
        'slug',
        'model',
        'series',
        'storage_gb',
        'color',
        'condition',
        'battery_health',
        'price',
        'original_price',
        'completeness',
        'warranty',
        'location',
        'description',
        'status',
        'views',
        'is_featured',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'condition' => ListingCondition::class,
            'warranty' => Warranty::class,
            'status' => ListingStatus::class,
            'completeness' => 'array',
            'price' => 'integer',
            'original_price' => 'integer',
            'storage_gb' => 'integer',
            'battery_health' => 'integer',
            'views' => 'integer',
            'is_featured' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(ListingPhoto::class)->orderBy('sort_order');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function chatThreads(): HasMany
    {
        return $this->hasMany(ChatThread::class);
    }

    public function storageLabel(): string
    {
        return $this->storage_gb >= 1024
            ? intdiv($this->storage_gb, 1024).' TB'
            : $this->storage_gb.' GB';
    }

    public function savings(): ?int
    {
        if ($this->original_price === null || $this->original_price <= $this->price) {
            return null;
        }

        return $this->original_price - $this->price;
    }

    public function isAvailable(): bool
    {
        return $this->status === ListingStatus::Active;
    }

    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('status', ListingStatus::Active);
    }

    #[Scope]
    protected function featured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    #[Scope]
    protected function latestFirst(Builder $query): Builder
    {
        return $query->orderByDesc('created_at')->orderByDesc('id');
    }
}
