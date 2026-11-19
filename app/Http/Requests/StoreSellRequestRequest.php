<?php

namespace App\Http\Requests;

use App\Enums\ListingCondition;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSellRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'model' => ['required', 'string', 'max:150'],
            'series' => ['nullable', 'string', 'max:10'],
            'storage_gb' => ['required', 'integer', 'min:16', 'max:2048'],
            'color' => ['required', 'string', 'max:100'],
            'condition' => ['required', Rule::enum(ListingCondition::class)],
            'battery_health' => ['nullable', 'integer', 'min:50', 'max:100'],
            'price' => ['required', 'integer', 'min:100000', 'max:100000000'],
            'description' => ['nullable', 'string', 'max:2000'],
            'city' => ['required', 'string', 'max:100'],
            'photos' => ['nullable', 'array', 'max:3'],
            'photos.*' => ['image', 'max:4096'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'model' => 'model iPhone',
            'storage_gb' => 'kapasitas penyimpanan',
            'color' => 'warna',
            'condition' => 'kondisi',
            'battery_health' => 'kesehatan baterai',
            'price' => 'harga harapan',
            'description' => 'deskripsi',
            'city' => 'kota',
            'photos' => 'foto',
            'photos.*' => 'foto',
        ];
    }
}
