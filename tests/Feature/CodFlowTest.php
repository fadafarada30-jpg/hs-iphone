<?php

namespace Tests\Feature;

use App\Enums\ListingStatus;
use App\Enums\OrderStatus;
use App\Enums\ProofStatus;
use App\Enums\Role;
use App\Enums\ShippingMethod;
use App\Models\Listing;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CodFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_cod_checkout_saves_schedule_and_keeps_listing_available(): void
    {
        $buyer = User::factory()->create(['city' => 'Jakarta', 'avatar_color' => '#0071e3', 'joined_at' => '2024-01-01']);
        $seller = User::factory()->admin()->create(['city' => 'Bandung', 'avatar_color' => '#34c759', 'joined_at' => '2024-01-01']);
        $driver = User::factory()->create([
            'role' => Role::Driver,
            'city' => 'Jakarta',
            'avatar_color' => '#ff9500',
            'joined_at' => '2024-01-01',
        ]);
        $listing = Listing::factory()->create(['seller_id' => $seller->id]);

        $response = $this->actingAs($buyer)
            ->withSession(['cart' => [$listing->id]])
            ->post('/checkout', [
                'recipient_name' => $buyer->name,
                'recipient_phone' => '081234567890',
                'shipping_city' => 'Jakarta',
                'shipping_address' => 'Jl. Contoh No. 10, Jakarta',
                'shipping_method' => ShippingMethod::Cod->value,
                'payment_method' => 'cod',
                'booking_date' => today()->addDay()->toDateString(),
                'booking_time' => '14:30',
                'cod_address' => 'Jl. Contoh No. 10, Jakarta',
            ]);

        $response->assertRedirect();

        $order = Order::firstOrFail();
        $this->assertSame($driver->id, $order->driver_id);
        $this->assertSame(ShippingMethod::Cod, $order->shipping_method);
        $this->assertSame(OrderStatus::Processing, $order->status);
        $this->assertSame('14:30', $order->booking_time);
        $this->assertSame('Jl. Contoh No. 10, Jakarta', $order->cod_address);
        $this->assertSame(ListingStatus::Active, $listing->fresh()->status);
    }

    public function test_booking_pages_render_for_customer_driver_and_admin(): void
    {
        $buyer = User::factory()->create(['city' => 'Jakarta', 'avatar_color' => '#0071e3', 'joined_at' => '2024-01-01']);
        $seller = User::factory()->admin()->create(['city' => 'Bandung', 'avatar_color' => '#34c759', 'joined_at' => '2024-01-01']);
        $driver = User::factory()->create(['role' => Role::Driver, 'city' => 'Jakarta', 'avatar_color' => '#ff9500', 'joined_at' => '2024-01-01']);
        $admin = User::factory()->create(['role' => Role::Admin, 'city' => 'Jakarta', 'avatar_color' => '#1d1d1f', 'joined_at' => '2024-01-01']);
        $listing = Listing::factory()->create(['seller_id' => $seller->id]);

        $order = Order::create([
            'code' => 'HSPAGERENDER',
            'user_id' => $buyer->id,
            'driver_id' => $driver->id,
            'subtotal' => $listing->price,
            'shipping_cost' => 0,
            'total' => $listing->price,
            'shipping_method' => ShippingMethod::Cod,
            'payment_method' => 'cod',
            'recipient_name' => $buyer->name,
            'recipient_phone' => '081234567890',
            'shipping_city' => 'Jakarta',
            'shipping_address' => 'Jl. Contoh No. 10, Jakarta',
            'booking_date' => today()->addDay(),
            'booking_time' => '14:30',
            'cod_address' => 'Jl. Contoh No. 10, Jakarta',
            'status' => OrderStatus::Processing,
        ]);
        OrderItem::create([
            'order_id' => $order->id,
            'listing_id' => $listing->id,
            'seller_id' => $seller->id,
            'model' => $listing->model,
            'storage_gb' => $listing->storage_gb,
            'color' => $listing->color,
            'price' => $listing->price,
        ]);

        $this->actingAs($buyer)->withSession(['cart' => [$listing->id]])->get('/checkout')->assertOk();
        $this->actingAs($buyer)->get(route('listings.show', $listing))->assertOk();
        $this->actingAs($driver)->get(route('driver.index'))->assertOk();
        $this->actingAs($driver)->get(route('driver.show', $order))->assertOk();
        $this->actingAs($admin)->get(route('admin.proofs'))->assertOk();
        $this->actingAs($buyer)->get(route('orders.show', $order))->assertOk();
    }

    public function test_driver_proof_is_approved_by_admin_and_notifies_buyer(): void
    {
        Storage::fake('public');

        $buyer = User::factory()->create(['city' => 'Jakarta', 'avatar_color' => '#0071e3', 'joined_at' => '2024-01-01']);
        $seller = User::factory()->admin()->create(['city' => 'Bandung', 'avatar_color' => '#34c759', 'joined_at' => '2024-01-01']);
        $driver = User::factory()->create(['role' => Role::Driver, 'city' => 'Jakarta', 'avatar_color' => '#ff9500', 'joined_at' => '2024-01-01']);
        $admin = User::factory()->create(['role' => Role::Admin, 'city' => 'Jakarta', 'avatar_color' => '#1d1d1f', 'joined_at' => '2024-01-01']);
        $listing = Listing::factory()->create(['seller_id' => $seller->id]);

        $order = Order::create([
            'code' => 'HSCODTEST',
            'user_id' => $buyer->id,
            'driver_id' => $driver->id,
            'subtotal' => $listing->price,
            'shipping_cost' => 0,
            'total' => $listing->price,
            'shipping_method' => ShippingMethod::Cod,
            'payment_method' => 'cod',
            'recipient_name' => $buyer->name,
            'recipient_phone' => '081234567890',
            'shipping_city' => 'Jakarta',
            'shipping_address' => 'Jl. Contoh No. 10, Jakarta',
            'booking_date' => today()->addDay(),
            'booking_time' => '14:30',
            'cod_address' => 'Jl. Contoh No. 10, Jakarta',
            'status' => OrderStatus::Processing,
        ]);
        OrderItem::create([
            'order_id' => $order->id,
            'listing_id' => $listing->id,
            'seller_id' => $seller->id,
            'model' => $listing->model,
            'storage_gb' => $listing->storage_gb,
            'color' => $listing->color,
            'price' => $listing->price,
        ]);

        $this->actingAs($driver)->post(route('driver.depart', $order), [
            'departure_photo_self' => UploadedFile::fake()->image('self.jpg'),
            'departure_photo_location' => UploadedFile::fake()->image('location.jpg'),
            'departure_notes' => 'Berangkat dari toko',
        ])->assertRedirect();

        $this->assertNotNull($order->fresh()->departed_at);
        $this->assertSame(OrderStatus::Shipped, $order->fresh()->status);

        $this->actingAs($driver)->post(route('driver.proof', $order), [
            'sold_proof_photo' => UploadedFile::fake()->image('proof.jpg'),
            'sold_proof_notes' => 'Unit sudah diserahkan',
        ])->assertRedirect();

        $this->assertSame(ProofStatus::Pending, $order->fresh()->sold_proof_status);

        $this->actingAs($admin)->patch(route('admin.proofs.approve', $order))->assertRedirect();

        $this->assertSame(ProofStatus::Approved, $order->fresh()->sold_proof_status);
        $this->assertSame(OrderStatus::Completed, $order->fresh()->status);
        $this->assertSame(ListingStatus::Sold, $listing->fresh()->status);
        $this->assertDatabaseHas('messages', [
            'sender_id' => $admin->id,
            'body' => 'Pesanan kamu untuk '.$listing->model.' '.$listing->storage_gb.'GB sudah dikonfirmasi. '
                .'Unit resmi menandai status SOLD setelah bukti dari driver disetujui admin. Terima kasih sudah booking di hs·iphone!',
        ]);
    }
}
