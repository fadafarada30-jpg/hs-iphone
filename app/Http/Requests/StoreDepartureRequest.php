<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDepartureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'departure_photo_self' => ['required', 'image', 'max:4096'],
            'departure_photo_location' => ['required', 'image', 'max:4096'],
            'departure_notes' => ['required', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'departure_photo_self' => 'foto selfie/barang',
            'departure_photo_location' => 'foto lokasi/alamat',
            'departure_notes' => 'keterangan keberangkatan',
        ];
    }
}
