<?php

namespace App\Actions;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\ShippingMethod;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Listing;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Services\ShoppingSession;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateOrder
{
    public function __construct(private readonly ShoppingSession $shopping) {}

    /**
     * Create an order from the current cart contents.
     */
    public function handle(StoreOrderRequest $request): Order
    {
        $listings = $this->shopping->cartListings();

        if ($listings->isEmpty()) {
            abort(422, 'Keranjang kosong.');
        }

        $shippingMethod = ShippingMethod::from($request->validated('shipping_method'));
        $subtotal = $listings->sum('price');
        $shippingCost = $shippingMethod->cost();
        $total = $subtotal + $shippingCost;

        // COD & pickup are paid on the spot and become confirmed bookings right away.
        $paymentMethod = $shippingMethod->isScheduled()
            ? PaymentMethod::Cod
            : PaymentMethod::from($request->validated('payment_method'));

        $status = $shippingMethod->isScheduled()
            ? OrderStatus::Processing
            : OrderStatus::AwaitingPayment;

        $driverId = $shippingMethod === ShippingMethod::Cod
            ? User::drivers()->first()?->id
            : null;

        return DB::transaction(function () use ($request, $listings, $shippingMethod, $paymentMethod, $status, $driverId, $subtotal, $shippingCost, $total) {
            $order = Order::create([
                'code' => 'HS'.strtoupper(Str::random(8)),
                'user_id' => $request->user()->id,
                'driver_id' => $driverId,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'total' => $total,
                'shipping_method' => $shippingMethod,
                'payment_method' => $paymentMethod,
                'recipient_name' => $request->validated('recipient_name'),
                'recipient_phone' => $request->validated('recipient_phone'),
                'shipping_city' => $request->validated('shipping_city'),
                'shipping_address' => $request->validated('shipping_address'),
                'booking_date' => $request->validated('booking_date'),
                'booking_time' => $request->validated('booking_time'),
                'cod_address' => $request->validated('cod_address'),
                'notes' => $request->validated('notes'),
                'status' => $status,
            ]);

            $listings->each(function (Listing $listing) use ($order) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'listing_id' => $listing->id,
                    'seller_id' => $listing->seller_id,
                    'model' => $listing->model,
                    'storage_gb' => $listing->storage_gb,
                    'color' => $listing->color,
                    'price' => $listing->price,
                ]);
            });

            $this->shopping->clearCart();

            return $order;
        });
    }
}
