<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSoldProofRequest extends FormRequest
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
            'sold_proof_photo' => ['required', 'image', 'max:4096'],
            'sold_proof_notes' => ['required', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'sold_proof_photo' => 'foto bukti sold',
            'sold_proof_notes' => 'keterangan bukti sold',
        ];
    }
}
