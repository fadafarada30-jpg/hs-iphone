<?php

namespace Tests\Feature;

use App\Models\Listing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_create_product_page(): void
    {
        $admin = User::factory()->admin()->create([
            'city' => 'Jakarta',
            'avatar_color' => '#000',
            'joined_at' => '2024-01-01',
        ]);

        $this->actingAs($admin)
            ->get('/admin/produk/tambah')
            ->assertStatus(200)
            ->assertSee('Tambah produk', false);
    }

    public function test_admin_can_create_product(): void
    {
        $admin = User::factory()->admin()->create([
            'city' => 'Jakarta',
            'avatar_color' => '#000',
            'joined_at' => '2024-01-01',
        ]);

        $response = $this->actingAs($admin)->post('/admin/produk', [
            'model' => 'iPhone 15 Pro',
            'series' => '15 Pro',
            'storage_gb' => 256,
            'color' => 'Natural Titanium',
            'condition' => 'like_new',
            'battery_health' => 98,
            'price' => 14500000,
            'original_price' => 15500000,
            'warranty' => 'official',
            'location' => 'Depok',
            'description' => 'Unit mulus, lengkap dengan box.',
            'status' => 'active',
            'completeness' => ['Box', 'Charger'],
        ]);

        $response->assertRedirect('/admin/produk');

        $this->assertDatabaseHas('listings', [
            'model' => 'iPhone 15 Pro',
            'seller_id' => $admin->id,
            'storage_gb' => 256,
            'status' => 'active',
            'price' => 14500000,
        ]);
    }

    public function test_admin_can_open_edit_product_page(): void
    {
        $admin = User::factory()->admin()->create([
            'city' => 'Jakarta',
            'avatar_color' => '#000',
            'joined_at' => '2024-01-01',
        ]);
        $listing = Listing::factory()->create(['seller_id' => $admin->id]);

        $this->actingAs($admin)
            ->get('/admin/produk/'.$listing->slug.'/edit')
            ->assertStatus(200)
            ->assertSee('Edit produk', false);
    }

    public function test_admin_can_update_product(): void
    {
        $admin = User::factory()->admin()->create([
            'city' => 'Jakarta',
            'avatar_color' => '#000',
            'joined_at' => '2024-01-01',
        ]);
        $listing = Listing::factory()->create(['seller_id' => $admin->id]);

        $response = $this->actingAs($admin)->put('/admin/produk/'.$listing->slug, [
            'model' => 'iPhone 14 Pro',
            'series' => '14 Pro',
            'storage_gb' => 512,
            'color' => 'Deep Purple',
            'condition' => 'excellent',
            'battery_health' => 92,
            'price' => 12000000,
            'warranty' => 'inter',
            'location' => 'Jakarta',
            'description' => 'Unit sudah diperbarui.',
            'status' => 'sold',
            'completeness' => ['Box'],
            'is_featured' => 1,
        ]);

        $response->assertRedirect('/admin/produk');

        $this->assertDatabaseHas('listings', [
            'id' => $listing->id,
            'model' => 'iPhone 14 Pro',
            'price' => 12000000,
            'status' => 'sold',
            'is_featured' => 1,
        ]);
    }

    public function test_buyer_cannot_create_product(): void
    {
        $buyer = User::factory()->create([
            'role' => 'buyer',
            'city' => 'Jakarta',
            'avatar_color' => '#000',
            'joined_at' => '2024-01-01',
        ]);

        $this->actingAs($buyer)
            ->get('/admin/produk/tambah')
            ->assertStatus(403);
    }
}
