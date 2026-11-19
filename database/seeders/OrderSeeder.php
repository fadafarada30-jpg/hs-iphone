<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\ShippingMethod;
use App\Models\Listing;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $budi = User::where('email', 'budi@demo.com')->first();
        $buyers = User::where('role', 'buyer')->get();
        $listings = Listing::with('seller')->where('status', 'active')->get();

        // 3 orders for Budi so his profile has history
        $this->createOrder($budi, $listings->random(), OrderStatus::Completed);
        $this->createOrder($budi, $listings->random(), OrderStatus::Shipped);
        $this->createOrder($budi, $listings->random(), OrderStatus::AwaitingPayment);

        // Random orders for other buyers
        $buyers->where('email', '!=', 'budi@demo.com')->take(8)->each(function (User $buyer) use ($listings) {
            $status = fake()->randomElement(OrderStatus::cases());
            $this->createOrder($buyer, $listings->random(), $status);
        });
    }

    private function createOrder(User $buyer, Listing $listing, OrderStatus $status): void
    {
        $shippingMethod = fake()->randomElement(ShippingMethod::cases());
        $shippingCost = $shippingMethod->cost();
        $subtotal = $listing->price;
        $total = $subtotal + $shippingCost;

        $order = Order::create([
            'code' => 'HS'.strtoupper(Str::random(8)),
            'user_id' => $buyer->id,
            'subtotal' => $subtotal,
            'shipping_cost' => $shippingCost,
            'total' => $total,
            'shipping_method' => $shippingMethod,
            'payment_method' => fake()->randomElement(PaymentMethod::cases()),
            'recipient_name' => $buyer->name,
            'recipient_phone' => '08'.fake()->numerify('##########'),
            'shipping_city' => $buyer->city,
            'shipping_address' => fake('id_ID')->address(),
            'notes' => fake()->boolean(30) ? fake('id_ID')->sentence() : null,
            'status' => $status,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'listing_id' => $listing->id,
            'seller_id' => $listing->seller_id,
            'model' => $listing->model,
            'storage_gb' => $listing->storage_gb,
            'color' => $listing->color,
            'price' => $listing->price,
        ]);
    }
}
