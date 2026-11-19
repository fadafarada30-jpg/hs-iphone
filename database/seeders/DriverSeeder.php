<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\Role;
use App\Enums\ShippingMethod;
use App\Models\Listing;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DriverSeeder extends Seeder
{
    public function run(): void
    {
        $driver = User::firstOrCreate(['email' => 'driver@demo.com'], [
            'name' => 'Rizky Driver',
            'role' => Role::Driver,
            'city' => 'Jakarta',
            'avatar_color' => '#ff9500',
            'joined_at' => '2024-01-01',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        if (! $driver->password) {
            $driver->forceFill(['password' => Hash::make('password')])->save();
        }

        $buyer = User::where('email', 'budi@demo.com')->first();
        $listings = Listing::active()->take(2)->get();

        if (! $buyer || $listings->count() < 2) {
            return;
        }

        $schedules = [
            ['code' => 'HSCODRIVER1', 'date' => today()->addDay(), 'time' => '14:00'],
            ['code' => 'HSCODRIVER2', 'date' => today()->addDays(2), 'time' => '10:30'],
        ];

        foreach ($schedules as $index => $schedule) {
            $listing = $listings[$index];

            $order = Order::firstOrCreate(['code' => $schedule['code']], [
                'user_id' => $buyer->id,
                'driver_id' => $driver->id,
                'subtotal' => $listing->price,
                'shipping_cost' => 0,
                'total' => $listing->price,
                'shipping_method' => ShippingMethod::Cod,
                'payment_method' => PaymentMethod::Cod,
                'recipient_name' => $buyer->name,
                'recipient_phone' => '081234567890',
                'shipping_city' => $buyer->city,
                'shipping_address' => 'Jl. Demo No. 12, '.$buyer->city,
                'booking_date' => $schedule['date'],
                'booking_time' => $schedule['time'],
                'cod_address' => 'Jl. Demo No. 12, '.$buyer->city,
                'status' => OrderStatus::Processing,
            ]);

            OrderItem::firstOrCreate([
                'order_id' => $order->id,
                'listing_id' => $listing->id,
            ], [
                'seller_id' => $listing->seller_id,
                'model' => $listing->model,
                'storage_gb' => $listing->storage_gb,
                'color' => $listing->color,
                'price' => $listing->price,
            ]);
        }
    }
}
