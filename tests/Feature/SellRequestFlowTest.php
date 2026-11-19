<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Enums\SellRequestStatus;
use App\Models\Listing;
use App\Models\SellRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SellRequestFlowTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->admin()->create([
            'city' => 'Depok',
            'avatar_color' => '#1d1d1f',
            'joined_at' => '2024-01-01',
        ]);
    }

    private function buyer(): User
    {
        return User::factory()->create([
            'city' => 'Depok',
            'avatar_color' => '#0071e3',
            'joined_at' => '2024-01-01',
        ]);
    }

    public function test_customer_can_submit_a_sell_request(): void
    {
        $buyer = $this->buyer();

        $this->actingAs($buyer)->get(route('sell.create'))->assertOk();

        $this->actingAs($buyer)->post(route('sell.store'), [
            'model' => 'iPhone 13',
            'series' => '13',
            'storage_gb' => 128,
            'color' => 'Midnight',
            'condition' => 'excellent',
            'battery_health' => 90,
            'price' => 4500000,
            'city' => 'Depok',
            'description' => 'Unit mulus, lengkap dengan kardus.',
        ])->assertRedirect(route('sell.index'));

        $this->assertDatabaseHas('sell_requests', [
            'user_id' => $buyer->id,
            'model' => 'iPhone 13',
            'status' => SellRequestStatus::Pending->value,
        ]);
    }

    public function test_admin_approval_only_opens_chat_without_publishing_listing(): void
    {
        $admin = $this->admin();
        $buyer = $this->buyer();

        $request = SellRequest::create([
            'user_id' => $buyer->id,
            'model' => 'iPhone 12',
            'series' => '12',
            'storage_gb' => 64,
            'color' => 'Blue',
            'condition' => 'good',
            'battery_health' => 84,
            'price' => 3000000,
            'city' => 'Depok',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.sell-requests.approve', $request));

        $thread = $request->fresh()->chatThread;
        $response->assertRedirect(route('chat.show', $thread));

        $this->assertSame(SellRequestStatus::Approved, $request->fresh()->status);
        $this->assertNull($request->fresh()->listing_id);
        $this->assertNull($thread->listing_id);
        $this->assertSame(0, Listing::count());
        $this->assertDatabaseHas('messages', [
            'chat_thread_id' => $thread->id,
            'sender_id' => $admin->id,
        ]);

        $this->actingAs($admin)->get(route('chat.show', $thread))->assertOk();
    }

    public function test_admin_rejection_notifies_buyer_in_chat(): void
    {
        $admin = $this->admin();
        $buyer = $this->buyer();

        $request = SellRequest::create([
            'user_id' => $buyer->id,
            'model' => 'iPhone 11',
            'series' => '11',
            'storage_gb' => 64,
            'color' => 'Black',
            'condition' => 'fair',
            'battery_health' => 70,
            'price' => 2000000,
            'city' => 'Depok',
        ]);

        $this->actingAs($admin)->post(route('admin.sell-requests.reject', $request), [
            'rejection_note' => 'Kondisi baterai tidak memenuhi syarat toko.',
        ])->assertRedirect();

        $fresh = $request->fresh();
        $this->assertSame(SellRequestStatus::Rejected, $fresh->status);
        $this->assertSame('Kondisi baterai tidak memenuhi syarat toko.', $fresh->rejection_note);
        $this->assertNull($fresh->chatThread->listing_id);
        $this->assertDatabaseHas('messages', [
            'chat_thread_id' => $fresh->chat_thread_id,
            'sender_id' => $admin->id,
        ]);

        $this->actingAs($buyer)->get(route('sell.index'))->assertSee('Kondisi baterai tidak memenuhi syarat toko.');
        $this->actingAs($buyer)->get(route('chat.index'))->assertOk();
        $this->actingAs($buyer)->get(route('chat.show', $fresh->chatThread))->assertOk();
    }
}
