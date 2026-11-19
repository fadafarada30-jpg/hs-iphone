<?php

namespace App\Http\Requests;

use App\Enums\ListingCondition;
use App\Enums\Warranty;
use App\Models\Listing;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Listing::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'model' => ['required', 'string', 'max:100'],
            'series' => ['required', 'string', 'max:10'],
            'storage_gb' => ['required', 'integer', Rule::in([32, 64, 128, 256, 512, 1024])],
            'color' => ['required', 'string', 'max:60'],
            'condition' => ['required', Rule::enum(ListingCondition::class)],
            'battery_health' => ['required', 'integer', 'min:50', 'max:100'],
            'price' => ['required', 'integer', 'min:500000', 'max:50000000'],
            'original_price' => ['nullable', 'integer', 'min:500000', 'max:50000000'],
            'completeness' => ['nullable', 'array'],
            'completeness.*' => ['string', 'max:60'],
            'warranty' => ['required', Rule::enum(Warranty::class)],
            'location' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'min:20', 'max:2000'],
            'photos' => ['nullable', 'array', 'max:5'],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'price.min' => 'Harga minimal Rp 500.000.',
            'battery_health.min' => 'Kapasitas baterai minimal 50%.',
            'description.min' => 'Deskripsi minimal 20 karakter.',
            'photos.*.max' => 'Ukuran foto maksimal 4 MB.',
        ];
    }
}
