<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\ProofStatus;
use App\Enums\ShippingMethod;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'user_id',
        'driver_id',
        'subtotal',
        'shipping_cost',
        'total',
        'shipping_method',
        'payment_method',
        'recipient_name',
        'recipient_phone',
        'shipping_city',
        'shipping_address',
        'booking_date',
        'booking_time',
        'cod_address',
        'notes',
        'departed_at',
        'departure_photo_self',
        'departure_photo_location',
        'departure_notes',
        'sold_proof_photo',
        'sold_proof_notes',
        'sold_proof_status',
        'sold_proof_rejected_note',
        'sold_proof_reviewed_by',
        'sold_proof_reviewed_at',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'shipping_method' => ShippingMethod::class,
            'payment_method' => PaymentMethod::class,
            'status' => OrderStatus::class,
            'sold_proof_status' => ProofStatus::class,
            'departed_at' => 'datetime',
            'booking_date' => 'date',
            'sold_proof_reviewed_at' => 'datetime',
            'subtotal' => 'integer',
            'shipping_cost' => 'integer',
            'total' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'code';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function proofReviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sold_proof_reviewed_by');
    }

    public function isCod(): bool
    {
        return $this->shipping_method === ShippingMethod::Cod;
    }

    public function isPickup(): bool
    {
        return $this->shipping_method === ShippingMethod::Pickup;
    }

    public function hasSchedule(): bool
    {
        return $this->shipping_method?->isScheduled() ?? false;
    }

    public function scheduleLabel(): string
    {
        if (! $this->hasSchedule() || ! $this->booking_date) {
            return '—';
        }

        return $this->booking_date->translatedFormat('d M Y').' · '.$this->booking_time;
    }

    public function recipientPhoneLink(): string
    {
        $digits = preg_replace('/\D+/', '', $this->recipient_phone ?? '');

        if ($digits === '') {
            return '';
        }

        if (str_starts_with($digits, '0')) {
            $digits = '62'.substr($digits, 1);
        } elseif (str_starts_with($digits, '8')) {
            $digits = '62'.$digits;
        }

        return 'tel:+'.$digits;
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function unitsCount(): int
    {
        return $this->items->sum(fn (OrderItem $item): int => 1);
    }

    public function isBuyer(User $user): bool
    {
        return $this->user_id === $user->id;
    }

    public function isSeller(User $user): bool
    {
        return $this->items->contains(fn (OrderItem $item): bool => $item->seller_id === $user->id);
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [OrderStatus::AwaitingPayment, OrderStatus::Processing], true);
    }

    #[Scope]
    protected function notCancelled(Builder $query): Builder
    {
        return $query->where('status', '!=', OrderStatus::Cancelled);
    }

    #[Scope]
    protected function latestFirst(Builder $query): Builder
    {
        return $query->orderByDesc('created_at')->orderByDesc('id');
    }

    #[Scope]
    protected function forDriver(Builder $query, int $driverId): Builder
    {
        return $query->where('driver_id', $driverId);
    }

    #[Scope]
    protected function pendingProof(Builder $query): Builder
    {
        return $query->where('sold_proof_status', ProofStatus::Pending);
    }
}
