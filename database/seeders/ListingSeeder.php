<?php

namespace Database\Seeders;

use App\Enums\ListingCondition;
use App\Enums\ListingStatus;
use App\Enums\Role;
use App\Enums\Warranty;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ListingSeeder extends Seeder
{
    /** @var list<array{model: string, series: string, storage: int, price: int, originalPrice: int|null, condition: ListingCondition, warranty: Warranty, color: string}> */
    private array $fixed = [];

    public function run(): void
    {
        $adminId = User::where('role', Role::Admin)->value('id');

        // HS Depok owns the stock: every listing belongs to the shop account.
        $this->createFixed($adminId, $adminId, $adminId);

        Listing::factory()
            ->count(28)
            ->create(['seller_id' => $adminId]);
    }

    private function createFixed(int $sitiId, int $ahmadId, int $dewiId): void
    {
        $specs = [
            // Siti listings
            ['seller_id' => $sitiId, 'model' => 'iPhone 16 Pro Max', 'series' => '16', 'storage_gb' => 256, 'price' => 20_500_000, 'original_price' => 24_999_000, 'condition' => ListingCondition::LikeNew, 'warranty' => Warranty::Official, 'color' => 'Black Titanium', 'is_featured' => true],
            ['seller_id' => $sitiId, 'model' => 'iPhone 15 Pro', 'series' => '15', 'storage_gb' => 256, 'price' => 13_200_000, 'original_price' => 16_999_000, 'condition' => ListingCondition::Excellent, 'warranty' => Warranty::Official, 'color' => 'Natural Titanium', 'is_featured' => true],
            ['seller_id' => $sitiId, 'model' => 'iPhone 14 Pro Max', 'series' => '14', 'storage_gb' => 512, 'price' => 12_800_000, 'original_price' => null, 'condition' => ListingCondition::Excellent, 'warranty' => Warranty::Inter, 'color' => 'Deep Purple', 'is_featured' => false],
            ['seller_id' => $sitiId, 'model' => 'iPhone 13 Pro Max', 'series' => '13', 'storage_gb' => 256, 'price' => 9_500_000, 'original_price' => 12_000_000, 'condition' => ListingCondition::Good, 'warranty' => Warranty::Expired, 'color' => 'Sierra Blue', 'is_featured' => false],
            ['seller_id' => $sitiId, 'model' => 'iPhone 16', 'series' => '16', 'storage_gb' => 128, 'price' => 12_800_000, 'original_price' => 14_999_000, 'condition' => ListingCondition::LikeNew, 'warranty' => Warranty::Official, 'color' => 'Ultramarine', 'is_featured' => true],
            ['seller_id' => $sitiId, 'model' => 'iPhone 16 Pro', 'series' => '16', 'storage_gb' => 512, 'price' => 19_500_000, 'original_price' => 22_999_000, 'condition' => ListingCondition::LikeNew, 'warranty' => Warranty::Official, 'color' => 'Desert Titanium', 'is_featured' => true],
            ['seller_id' => $sitiId, 'model' => 'iPhone 15 Pro Max', 'series' => '15', 'storage_gb' => 512, 'price' => 17_000_000, 'original_price' => 20_499_000, 'condition' => ListingCondition::Excellent, 'warranty' => Warranty::Official, 'color' => 'Blue Titanium', 'is_featured' => false],
            // Ahmad listings
            ['seller_id' => $ahmadId, 'model' => 'iPhone 14', 'series' => '14', 'storage_gb' => 128, 'price' => 7_800_000, 'original_price' => 10_999_000, 'condition' => ListingCondition::Excellent, 'warranty' => Warranty::Official, 'color' => 'Midnight', 'is_featured' => false],
            ['seller_id' => $ahmadId, 'model' => 'iPhone 14 Pro', 'series' => '14', 'storage_gb' => 256, 'price' => 10_200_000, 'original_price' => 13_999_000, 'condition' => ListingCondition::Excellent, 'warranty' => Warranty::Inter, 'color' => 'Space Black', 'is_featured' => true],
            ['seller_id' => $ahmadId, 'model' => 'iPhone 15', 'series' => '15', 'storage_gb' => 256, 'price' => 10_800_000, 'original_price' => 13_499_000, 'condition' => ListingCondition::LikeNew, 'warranty' => Warranty::Official, 'color' => 'Pink', 'is_featured' => false],
            ['seller_id' => $ahmadId, 'model' => 'iPhone 13', 'series' => '13', 'storage_gb' => 128, 'price' => 6_200_000, 'original_price' => 9_999_000, 'condition' => ListingCondition::Good, 'warranty' => Warranty::Expired, 'color' => 'Starlight', 'is_featured' => false],
            // Dewi listings
            ['seller_id' => $dewiId, 'model' => 'iPhone 11', 'series' => '11', 'storage_gb' => 128, 'price' => 3_200_000, 'original_price' => 6_999_000, 'condition' => ListingCondition::Good, 'warranty' => Warranty::Expired, 'color' => 'Black', 'is_featured' => false],
            ['seller_id' => $dewiId, 'model' => 'iPhone 12', 'series' => '12', 'storage_gb' => 64, 'price' => 4_100_000, 'original_price' => 7_999_000, 'condition' => ListingCondition::Excellent, 'warranty' => Warranty::Expired, 'color' => 'Blue', 'is_featured' => false],
            ['seller_id' => $dewiId, 'model' => 'iPhone 12 Pro', 'series' => '12', 'storage_gb' => 256, 'price' => 6_500_000, 'original_price' => null, 'condition' => ListingCondition::Good, 'warranty' => Warranty::Inter, 'color' => 'Gold', 'is_featured' => false],
            ['seller_id' => $dewiId, 'model' => 'iPhone 13 Pro', 'series' => '13', 'storage_gb' => 256, 'price' => 8_900_000, 'original_price' => 12_499_000, 'condition' => ListingCondition::Excellent, 'warranty' => Warranty::Official, 'color' => 'Graphite', 'is_featured' => true],
        ];

        $cities = ['Jakarta', 'Surabaya', 'Bandung', 'Yogyakarta', 'Semarang', 'Medan'];
        $completenessPool = ['Kardus original', 'Charger original', 'Earphone original', 'Kabel original', 'SIM ejector'];

        foreach ($specs as $spec) {
            $slug = Str::slug("{$spec['model']} {$spec['storage_gb']}gb {$spec['condition']->value}").'-'.Str::random(5);
            $battery = fake()->numberBetween(82, 100);
            $completeness = fake()->randomElements($completenessPool, fake()->numberBetween(2, 5));
            $location = fake()->randomElement($cities);

            Listing::create([
                'seller_id' => $spec['seller_id'],
                'slug' => $slug,
                'model' => $spec['model'],
                'series' => $spec['series'],
                'storage_gb' => $spec['storage_gb'],
                'color' => $spec['color'],
                'condition' => $spec['condition'],
                'battery_health' => $battery,
                'price' => $spec['price'],
                'original_price' => $spec['original_price'],
                'completeness' => $completeness,
                'warranty' => $spec['warranty'],
                'location' => $location,
                'description' => "Unit {$spec['model']} storage {$spec['storage_gb']}GB warna {$spec['color']} dengan kondisi {$spec['condition']->label()}. Battery health {$battery}%. Kelengkapan: ".implode(', ', $completeness).". {$spec['warranty']->label()}. Lokasi di {$location}. Unit siap dilihat langsung dan bisa COD atau ambil di toko.",
                'status' => ListingStatus::Active,
                'views' => fake()->numberBetween(10, 800),
                'is_featured' => $spec['is_featured'],
            ]);
        }
    }
}
