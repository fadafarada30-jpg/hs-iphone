<?php

namespace Tests\Feature;

use App\Models\ChatThread;
use App\Models\Listing;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ChatRealtimeTest extends TestCase
{
    use RefreshDatabase;

    private function conversation(): array
    {
        $buyer = User::factory()->create([
            'city' => 'Jakarta',
            'avatar_color' => '#0071e3',
            'joined_at' => '2024-01-01',
        ]);
        $seller = User::factory()->admin()->create([
            'city' => 'Depok',
            'avatar_color' => '#000000',
            'joined_at' => '2024-01-01',
        ]);
        $listing = Listing::factory()->create(['seller_id' => $seller->id]);
        $thread = ChatThread::create([
            'listing_id' => $listing->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'last_message_at' => now(),
        ]);

        return [$buyer, $seller, $thread];
    }

    public function test_poll_returns_only_new_messages_and_typing_state(): void
    {
        Cache::flush();
        [$buyer, $seller, $thread] = $this->conversation();
        $first = Message::create([
            'chat_thread_id' => $thread->id,
            'sender_id' => $seller->id,
            'body' => 'Halo, unit masih tersedia.',
        ]);

        $this->actingAs($seller)->postJson(route('chat.typing', $thread))->assertOk();

        $response = $this->actingAs($buyer)
            ->getJson(route('chat.poll', $thread).'?after='.$first->id)
            ->assertOk()
            ->assertJsonPath('typing', true);

        $response->assertJsonCount(0, 'messages');

        $second = Message::create([
            'chat_thread_id' => $thread->id,
            'sender_id' => $seller->id,
            'body' => 'Saya kirim detailnya ya.',
        ]);

        $this->actingAs($buyer)
            ->getJson(route('chat.poll', $thread).'?after='.$first->id)
            ->assertOk()
            ->assertJsonPath('messages.0.id', $second->id)
            ->assertJsonPath('messages.0.mine', false);
    }

    public function test_ajax_send_returns_message_instantly_and_tracks_unread(): void
    {
        Cache::flush();
        [$buyer, $seller, $thread] = $this->conversation();

        $this->actingAs($seller)
            ->postJson(route('chat.store', $thread), ['body' => 'Halo kak, bisa COD hari ini?'])
            ->assertCreated()
            ->assertJsonPath('message.mine', true)
            ->assertJsonPath('message.body', 'Halo kak, bisa COD hari ini?');

        $this->assertDatabaseHas('messages', [
            'chat_thread_id' => $thread->id,
            'sender_id' => $seller->id,
            'body' => 'Halo kak, bisa COD hari ini?',
        ]);
        $this->assertSame(1, $thread->fresh()->unread_count);

        $this->actingAs($buyer)
            ->postJson(route('chat.store', $thread), ['body' => 'Bisa kak'])
            ->assertCreated()
            ->assertJsonPath('message.mine', true);

        $this->assertSame(0, $thread->fresh()->unread_count);
    }

    public function test_thread_list_poll_returns_conversations(): void
    {
        [$buyer, , $thread] = $this->conversation();

        $this->actingAs($buyer)
            ->getJson(route('chat.poll-index'))
            ->assertOk()
            ->assertJsonPath('threads.0.id', $thread->id);
    }

    public function test_clear_removes_all_messages_and_resets_thread(): void
    {
        [$buyer, $seller, $thread] = $this->conversation();
        Message::create([
            'chat_thread_id' => $thread->id,
            'sender_id' => $buyer->id,
            'body' => 'Pesan pertama',
        ]);
        Message::create([
            'chat_thread_id' => $thread->id,
            'sender_id' => $seller->id,
            'body' => 'Pesan kedua',
        ]);
        $thread->update(['unread_count' => 2]);

        $this->actingAs($buyer)
            ->delete(route('chat.clear', $thread))
            ->assertRedirect(route('chat.show', $thread));

        $this->assertSame(0, Message::where('chat_thread_id', $thread->id)->count());
        $this->assertSame(0, $thread->fresh()->unread_count);
        $this->assertNull($thread->fresh()->last_message_at);
        $this->assertNotNull($thread->fresh()->cleared_at);
        $this->assertDatabaseHas('chat_threads', ['id' => $thread->id]);
    }

    public function test_clear_is_signalled_to_other_party_via_poll(): void
    {
        [$buyer, $seller, $thread] = $this->conversation();
        Message::create([
            'chat_thread_id' => $thread->id,
            'sender_id' => $buyer->id,
            'body' => 'Pesan yang akan dihapus',
        ]);

        $this->actingAs($seller)
            ->deleteJson(route('chat.clear', $thread))
            ->assertOk()
            ->assertJsonPath('ok', true);

        $response = $this->actingAs($buyer)
            ->getJson(route('chat.poll', $thread))
            ->assertOk();

        $this->assertSame([], $response->json('messages'));
        $this->assertGreaterThan(0, $response->json('cleared_at'));
    }

    public function test_destroy_removes_conversation_from_list(): void
    {
        [$buyer, $seller, $thread] = $this->conversation();
        Message::create([
            'chat_thread_id' => $thread->id,
            'sender_id' => $seller->id,
            'body' => 'Pesan yang ikut terhapus',
        ]);

        $this->actingAs($buyer)
            ->delete(route('chat.destroy', $thread))
            ->assertRedirect(route('chat.index'));

        $this->assertDatabaseMissing('chat_threads', ['id' => $thread->id]);
        $this->assertSame(0, Message::where('chat_thread_id', $thread->id)->count());

        $this->actingAs($seller)
            ->getJson(route('chat.poll-index'))
            ->assertOk()
            ->assertJsonCount(0, 'threads');
    }

    public function test_chat_timestamps_use_wib_and_expose_fresh_age(): void
    {
        [$buyer, , $thread] = $this->conversation();
        $message = Message::create([
            'chat_thread_id' => $thread->id,
            'sender_id' => $buyer->id,
            'body' => 'Cek waktu WIB',
        ]);

        $this->assertSame('Asia/Jakarta', config('app.timezone'));
        $this->assertSame('Asia/Jakarta', $message->fresh()->created_at->getTimezone()->getName());

        $response = $this->actingAs($buyer)
            ->getJson(route('chat.poll-index'))
            ->assertOk();

        $this->assertArrayHasKey('age', $response->json('threads.0.last'));
        $this->assertGreaterThanOrEqual(0, $response->json('threads.0.last.age'));
    }

    public function test_outsider_cannot_poll_signal_typing_clear_or_destroy(): void
    {
        [, , $thread] = $this->conversation();
        $outsider = User::factory()->create([
            'city' => 'Bandung',
            'avatar_color' => '#ff9500',
            'joined_at' => '2024-01-01',
        ]);

        $this->actingAs($outsider)->getJson(route('chat.poll', $thread))->assertForbidden();
        $this->actingAs($outsider)->postJson(route('chat.typing', $thread))->assertForbidden();
        $this->actingAs($outsider)->deleteJson(route('chat.clear', $thread))->assertForbidden();
        $this->actingAs($outsider)->deleteJson(route('chat.destroy', $thread))->assertForbidden();
    }
}
