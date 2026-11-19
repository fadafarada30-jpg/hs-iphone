<?php

namespace Database\Seeders;

use App\Models\ChatThread;
use App\Models\Listing;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Seeder;

class ChatSeeder extends Seeder
{
    public function run(): void
    {
        $budi = User::where('email', 'budi@demo.com')->first();
        $listings = Listing::with('seller')->where('status', 'active')->get();

        $autoReplies = [
            'Halo, terima kasih sudah tertarik! Unit masih tersedia 👍',
            'Bisa COD di area kita, atau kirim via JNE/Sicepat.',
            'Kondisinya masih mulus, baterai 90%+ terjamin.',
            'Harga bisa sedikit nego, tapi tidak terlalu ya 😊',
            'Sudah termasuk kardus dan charger original.',
            'Silahkan cek langsung kalau ingin melihat fisik unit.',
        ];

        // Create threads for Budi (demo buyer) with first 4 listings
        $listings->take(4)->each(function (Listing $listing) use ($budi, $autoReplies) {
            if ($listing->seller_id === $budi->id) {
                return;
            }

            $thread = ChatThread::create([
                'listing_id' => $listing->id,
                'buyer_id' => $budi->id,
                'seller_id' => $listing->seller_id,
                'unread_count' => 1,
                'last_message_at' => now()->subMinutes(fake()->numberBetween(10, 1440)),
            ]);

            Message::create([
                'chat_thread_id' => $thread->id,
                'sender_id' => $budi->id,
                'body' => "Halo, apakah {$listing->model} {$listing->storage_gb}GB ini masih tersedia?",
            ]);

            Message::create([
                'chat_thread_id' => $thread->id,
                'sender_id' => $listing->seller_id,
                'body' => fake()->randomElement($autoReplies),
            ]);
        });

        // Create a few extra threads between random buyers and sellers
        $buyers = User::where('role', 'buyer')->where('email', '!=', 'budi@demo.com')->take(5)->get();
        $randomListings = $listings->slice(4, 6);

        $buyers->each(function (User $buyer) use ($randomListings, $autoReplies) {
            $listing = $randomListings->random();

            if ($listing->seller_id === $buyer->id) {
                return;
            }

            $exists = ChatThread::where('listing_id', $listing->id)->where('buyer_id', $buyer->id)->exists();
            if ($exists) {
                return;
            }

            $thread = ChatThread::create([
                'listing_id' => $listing->id,
                'buyer_id' => $buyer->id,
                'seller_id' => $listing->seller_id,
                'unread_count' => 0,
                'last_message_at' => now()->subHours(fake()->numberBetween(1, 48)),
            ]);

            Message::create([
                'chat_thread_id' => $thread->id,
                'sender_id' => $buyer->id,
                'body' => "Pak/Bu, {$listing->model}nya masih ada?",
            ]);

            Message::create([
                'chat_thread_id' => $thread->id,
                'sender_id' => $listing->seller_id,
                'body' => fake()->randomElement($autoReplies),
            ]);
        });
    }
}
