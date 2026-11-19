<?php

namespace App\Services;

use App\Enums\Role;
use App\Models\ChatThread;
use App\Models\Listing;
use App\Models\Message;
use App\Models\Order;
use App\Models\User;

class BookingNotifier
{
    /**
     * Notify everyone who booked this listing that the unit is officially sold.
     * The message is sent from the shop admin so it appears in the existing chat.
     */
    public function notifySold(Listing $listing, ?User $sender = null): void
    {
        $sender ??= User::where('role', Role::Admin)->first() ?? $listing->seller;

        $body = 'Pesanan kamu untuk '.$listing->model.' '.$listing->storage_gb.'GB sudah dikonfirmasi. '
            .'Unit resmi menandai status SOLD setelah bukti dari driver disetujui admin. Terima kasih sudah booking di hs·iphone!';

        Order::notCancelled()
            ->whereHas('items', fn ($query) => $query->where('listing_id', $listing->id))
            ->with('user')
            ->each(function (Order $order) use ($listing, $sender, $body) {
                if ($order->user_id === $sender->id) {
                    return;
                }

                $thread = ChatThread::firstOrCreate(
                    [
                        'listing_id' => $listing->id,
                        'buyer_id' => $order->user_id,
                    ],
                    [
                        'seller_id' => $listing->seller_id,
                        'unread_count' => 0,
                    ]
                );

                // Avoid spamming the same notification when admin re-approves.
                if ($thread->messages()->latest()->first()?->body === $body) {
                    return;
                }

                Message::create([
                    'chat_thread_id' => $thread->id,
                    'sender_id' => $sender->id,
                    'body' => $body,
                ]);

                $thread->update([
                    'last_message_at' => now(),
                    'unread_count' => min($thread->unread_count + 1, 255),
                ]);
            });
    }
}
