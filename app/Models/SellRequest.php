<?php

namespace App\Models;

use App\Enums\ListingCondition;
use App\Enums\SellRequestStatus;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'model',
        'series',
        'storage_gb',
        'color',
        'condition',
        'battery_health',
        'price',
        'description',
        'city',
        'photos',
        'status',
        'rejection_note',
        'reviewed_by',
        'reviewed_at',
        'listing_id',
        'chat_thread_id',
    ];

    protected function casts(): array
    {
        return [
            'status' => SellRequestStatus::class,
            'condition' => ListingCondition::class,
            'photos' => 'array',
            'storage_gb' => 'integer',
            'battery_health' => 'integer',
            'price' => 'integer',
            'reviewed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function chatThread(): BelongsTo
    {
        return $this->belongsTo(ChatThread::class);
    }

    #[Scope]
    protected function pending(Builder $query): Builder
    {
        return $query->where('status', SellRequestStatus::Pending);
    }

    public function storageLabel(): string
    {
        return $this->storage_gb >= 1024
            ? intdiv($this->storage_gb, 1024).' TB'
            : $this->storage_gb.' GB';
    }
}
