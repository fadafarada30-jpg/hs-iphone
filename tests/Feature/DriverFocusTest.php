<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DriverFocusTest extends TestCase
{
    use RefreshDatabase;

    private function driver(): User
    {
        return User::factory()->create([
            'role' => Role::Driver,
            'city' => 'Jakarta',
            'avatar_color' => '#ff9500',
            'joined_at' => '2024-01-01',
        ]);
    }

    public function test_driver_login_redirects_to_cod_schedule(): void
    {
        $driver = $this->driver();

        $response = $this->post('/masuk', [
            'email' => $driver->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/driver');
        $this->assertAuthenticatedAs($driver);
    }

    public function test_driver_is_kept_out_of_storefront_and_admin_features(): void
    {
        $driver = $this->driver();

        $this->actingAs($driver)->get('/')->assertRedirect('/driver');
        $this->actingAs($driver)->get('/iphone')->assertRedirect('/driver');
        $this->actingAs($driver)->get(route('cart'))->assertRedirect('/driver');
        $this->actingAs($driver)->get(route('wishlist'))->assertRedirect('/driver');
        $this->actingAs($driver)->get(route('chat.index'))->assertRedirect('/driver');
        $this->actingAs($driver)->get(route('orders.index'))->assertRedirect('/driver');
        $this->actingAs($driver)->get(route('account'))->assertRedirect('/driver');
        $this->actingAs($driver)->get(route('sell.create'))->assertRedirect('/driver');
        $this->actingAs($driver)->get(route('admin.dashboard'))->assertRedirect('/driver');
        $this->actingAs($driver)->get('/masuk')->assertRedirect('/driver');
    }

    public function test_driver_only_sees_cod_schedule_workspace(): void
    {
        $driver = $this->driver();

        $response = $this->actingAs($driver)->get(route('driver.index'));

        $response->assertOk()
            ->assertSee('Jadwal COD saya.', false)
            ->assertSee('Jadwal aktif', false);
        $response->assertDontSee('Wishlist');
        $response->assertDontSee('Keranjang');
        $response->assertDontSee('Konsol Admin');
    }

    public function test_buyer_still_has_access_to_storefront_features(): void
    {
        $buyer = User::factory()->create([
            'role' => Role::Buyer,
            'city' => 'Jakarta',
            'avatar_color' => '#0071e3',
            'joined_at' => '2024-01-01',
        ]);

        $this->actingAs($buyer)->get('/')->assertOk();
        $this->actingAs($buyer)->get(route('cart'))->assertOk();
        $this->actingAs($buyer)->get(route('account'))->assertOk();
    }

    public function test_order_builds_a_tappable_customer_phone_link(): void
    {
        $order = new Order([
            'recipient_name' => 'Rizky',
            'recipient_phone' => '0812-3456-7890',
        ]);

        $this->assertSame('tel:+6281234567890', $order->recipientPhoneLink());
        $this->assertSame('', (new Order(['recipient_phone' => null]))->recipientPhoneLink());
    }
}
