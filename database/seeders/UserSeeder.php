<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $demos = [
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@demo.com',
                'role' => 'buyer',
                'city' => 'Jakarta',
                'avatar_color' => '#0071e3',
                'rating' => 5.0,
                'reviews_count' => 0,
                'bio' => 'Kolektor iPhone sejak 2015. Suka unit mulus kondisi prima.',
                'joined_at' => '2023-01-15',
            ],
            [
                'name' => 'Admin HS Depok',
                'email' => 'admin@demo.com',
                'role' => 'admin',
                'city' => 'Depok',
                'avatar_color' => '#1d1d1f',
                'rating' => 5.0,
                'reviews_count' => 0,
                'bio' => null,
                'joined_at' => '2022-01-01',
            ],
        ];

        foreach ($demos as $data) {
            $user = User::firstOrCreate(['email' => $data['email']], array_merge($data, [
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]));

            if (! $user->password) {
                $user->forceFill(['password' => Hash::make('password')])->save();
            }
        }

        User::factory()->count(20)->create();
    }
}
