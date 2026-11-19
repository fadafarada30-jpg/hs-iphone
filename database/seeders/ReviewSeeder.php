<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $buyers = User::where('role', Role::Buyer)->get();
        $shops = User::where('role', Role::Admin)->with('listings')->get();

        $shops->each(function (User $shop) use ($buyers) {
            $count = fake()->numberBetween(3, 8);
            $reviewers = $buyers->random(min($count, $buyers->count()));
            $listing = $shop->listings->first();

            $reviewers->each(function (User $buyer) use ($shop, $listing) {
                Review::create([
                    'reviewer_id' => $buyer->id,
                    'seller_id' => $shop->id,
                    'listing_id' => $listing?->id,
                    'rating' => fake()->numberBetween(3, 5),
                    'body' => fake('id_ID')->paragraph(2),
                ]);
            });

            $avg = $shop->reviewsReceived()->avg('rating');
            $total = $shop->reviewsReceived()->count();
            $shop->update([
                'rating' => round((float) ($avg ?? 5.0), 1),
                'reviews_count' => $total,
            ]);
        });
    }
}
