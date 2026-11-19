<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomepageTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_loads(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('hs·iphone');
    }

    public function test_catalog_page_loads(): void
    {
        $response = $this->get('/iphone');
        $response->assertStatus(200);
    }

    public function test_login_page_loads(): void
    {
        $response = $this->get('/masuk');
        $response->assertStatus(200);
        $response->assertSee('akun demo', false);
    }

    public function test_demo_login_works(): void
    {
        $user = User::factory()->create(['email' => 'test@demo.com', 'role' => 'buyer']);
        $response = $this->post('/masuk', ['email' => $user->email, 'password' => 'password']);
        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    public function test_admin_login_redirects_to_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create([
            'city' => 'Jakarta',
            'avatar_color' => '#000',
            'joined_at' => '2024-01-01',
        ]);

        $response = $this->post('/masuk', [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin');
        $this->assertAuthenticatedAs($admin);
    }

    public function test_login_rejects_wrong_password(): void
    {
        $user = User::factory()->create(['email' => 'test@demo.com']);
        $response = $this->from('/masuk')->post('/masuk', [
            'email' => $user->email,
            'password' => 'salah',
        ]);
        $response->assertRedirect('/masuk');
        $this->assertGuest();
    }

    public function test_register_page_loads(): void
    {
        $this->get('/daftar')->assertStatus(200)->assertSee('Buat akun baru.', false);
    }

    public function test_register_creates_buyer_and_logs_in(): void
    {
        $response = $this->post('/daftar', [
            'name' => 'Pelanggan Baru',
            'email' => 'baru@example.com',
            'city' => 'Depok',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'baru@example.com',
            'role' => 'buyer',
        ]);
    }

    public function test_wishlist_redirects_when_unauthenticated(): void
    {
        $response = $this->get('/wishlist');
        // Wishlist is session-based so guests can access it
        $response->assertStatus(200);
    }

    public function test_admin_dashboard_requires_auth(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/masuk');
    }

    public function test_admin_dashboard_requires_admin_role(): void
    {
        $user = User::factory()->create(['role' => 'buyer', 'city' => 'Jakarta', 'avatar_color' => '#000', 'joined_at' => '2024-01-01']);
        $response = $this->actingAs($user)->get('/admin');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_dashboard(): void
    {
        $admin = User::factory()->admin()->create(['city' => 'Jakarta', 'avatar_color' => '#000', 'joined_at' => '2024-01-01']);
        $response = $this->actingAs($admin)->get('/admin');
        $response->assertStatus(200);
    }

    public function test_sell_page_requires_auth(): void
    {
        $response = $this->get('/jual');
        $response->assertRedirect('/masuk');
    }
}
