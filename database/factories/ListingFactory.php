<?php

namespace Database\Factories;

use App\Enums\ListingCondition;
use App\Enums\ListingStatus;
use App\Enums\Warranty;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Listing>
 */
class ListingFactory extends Factory
{
    /** @var array<int, array{model: string, series: string, basePrices: array<int, int>}> */
    private static array $catalog = [
        ['model' => 'iPhone 11', 'series' => '11', 'basePrices' => [64 => 3_200_000, 128 => 3_600_000, 256 => 4_100_000]],
        ['model' => 'iPhone 12', 'series' => '12', 'basePrices' => [64 => 4_500_000, 128 => 5_000_000, 256 => 5_600_000]],
        ['model' => 'iPhone 12 Pro', 'series' => '12', 'basePrices' => [128 => 6_200_000, 256 => 7_000_000, 512 => 7_900_000]],
        ['model' => 'iPhone 13', 'series' => '13', 'basePrices' => [128 => 6_500_000, 256 => 7_200_000, 512 => 8_200_000]],
        ['model' => 'iPhone 13 Pro', 'series' => '13', 'basePrices' => [128 => 8_500_000, 256 => 9_500_000, 512 => 10_500_000]],
        ['model' => 'iPhone 13 Pro Max', 'series' => '13', 'basePrices' => [128 => 9_500_000, 256 => 10_500_000, 512 => 11_500_000]],
        ['model' => 'iPhone 14', 'series' => '14', 'basePrices' => [128 => 8_500_000, 256 => 9_500_000, 512 => 10_800_000]],
        ['model' => 'iPhone 14 Pro', 'series' => '14', 'basePrices' => [128 => 10_500_000, 256 => 11_500_000, 512 => 13_000_000]],
        ['model' => 'iPhone 14 Pro Max', 'series' => '14', 'basePrices' => [128 => 11_500_000, 256 => 12_500_000, 512 => 14_000_000]],
        ['model' => 'iPhone 15', 'series' => '15', 'basePrices' => [128 => 10_500_000, 256 => 11_500_000, 512 => 13_000_000]],
        ['model' => 'iPhone 15 Pro', 'series' => '15', 'basePrices' => [128 => 13_000_000, 256 => 14_500_000, 512 => 16_500_000]],
        ['model' => 'iPhone 15 Pro Max', 'series' => '15', 'basePrices' => [256 => 16_500_000, 512 => 18_500_000, 1024 => 20_000_000]],
        ['model' => 'iPhone 16', 'series' => '16', 'basePrices' => [128 => 13_500_000, 256 => 15_000_000, 512 => 17_000_000]],
        ['model' => 'iPhone 16 Pro', 'series' => '16', 'basePrices' => [128 => 17_000_000, 256 => 19_000_000, 512 => 21_000_000]],
        ['model' => 'iPhone 16 Pro Max', 'series' => '16', 'basePrices' => [256 => 21_000_000, 512 => 23_500_000, 1024 => 26_000_000]],
    ];

    /** @var list<string> */
    private static array $colors = [
        'Space Black', 'Silver', 'Gold', 'Deep Purple', 'Midnight',
        'Starlight', 'Blue', 'Green', 'Pink', 'Natural Titanium',
        'Blue Titanium', 'White Titanium', 'Black Titanium',
        'Desert Titanium', 'Ultramarine', 'Teal', 'Pink',
    ];

    /** @var list<string> */
    private static array $completenessOptions = [
        'Kardus original', 'Charger original', 'Earphone original',
        'Kabel original', 'SIM ejector', 'Buku panduan',
    ];

    /** @var list<string> */
    private static array $cities = [
        'Jakarta', 'Surabaya', 'Bandung', 'Medan', 'Semarang',
        'Makassar', 'Palembang', 'Yogyakarta', 'Tangerang', 'Depok',
    ];

    public function definition(): array
    {
        $entry = fake()->randomElement(self::$catalog);
        $storages = array_keys($entry['basePrices']);
        $storage = fake()->randomElement($storages);
        $basePrice = $entry['basePrices'][$storage];

        $condition = fake()->randomElement(ListingCondition::cases());
        $price = (int) round($basePrice * $condition->priceFactor() / 1_000) * 1_000;
        $originalPrice = fake()->boolean(60) ? $basePrice : null;

        $completeness = fake()->randomElements(self::$completenessOptions, fake()->numberBetween(2, 5));
        $warranty = fake()->randomElement(Warranty::cases());
        $color = fake()->randomElement(self::$colors);
        $battery = fake()->numberBetween(78, 100);
        $location = fake()->randomElement(self::$cities);

        $title = "{$entry['model']} {$storage}GB {$condition->label()}";

        return [
            'seller_id' => User::factory()->admin(),
            'slug' => Str::slug($title).'-'.Str::random(6),
            'model' => $entry['model'],
            'series' => $entry['series'],
            'storage_gb' => $storage,
            'color' => $color,
            'condition' => $condition,
            'battery_health' => $battery,
            'price' => $price,
            'original_price' => $originalPrice,
            'completeness' => $completeness,
            'warranty' => $warranty,
            'location' => $location,
            'description' => "Unit {$entry['model']} storage {$storage}GB warna {$color} dengan kondisi {$condition->label()}. Battery health {$battery}%. Kelengkapan: ".implode(', ', $completeness).". {$warranty->label()}. Lokasi di {$location}. Unit siap dilihat langsung dan bisa COD atau ambil di toko.",
            'status' => ListingStatus::Active,
            'views' => fake()->numberBetween(0, 500),
            'is_featured' => fake()->boolean(15),
        ];
    }

    public function active(): static
    {
        return $this->state(['status' => ListingStatus::Active]);
    }

    public function pending(): static
    {
        return $this->state(['status' => ListingStatus::PendingReview]);
    }

    public function featured(): static
    {
        return $this->state(['is_featured' => true, 'status' => ListingStatus::Active]);
    }

    public function forSeries(string $series): static
    {
        $entries = array_values(array_filter(self::$catalog, fn ($e) => $e['series'] === $series));

        return $this->state(function () use ($entries) {
            $entry = fake()->randomElement($entries);
            $storage = fake()->randomElement(array_keys($entry['basePrices']));

            return [
                'model' => $entry['model'],
                'series' => $entry['series'],
                'storage_gb' => $storage,
            ];
        });
    }
}
