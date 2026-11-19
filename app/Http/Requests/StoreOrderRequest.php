<?php

namespace App\Http\Requests;

use App\Enums\PaymentMethod;
use App\Enums\ShippingMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
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
            'recipient_name' => ['required', 'string', 'max:150'],
            'recipient_phone' => ['required', 'string', 'max:30'],
            'shipping_city' => ['required', 'string', 'max:100'],
            'shipping_address' => ['required', 'string', 'min:10', 'max:500'],
            'shipping_method' => ['required', Rule::enum(ShippingMethod::class)],
            'payment_method' => ['required', Rule::enum(PaymentMethod::class)],

            'booking_date' => [
                Rule::requiredIf($this->isScheduled()),
                'nullable',
                'date',
                'after_or_equal:today',
            ],
            'booking_time' => [
                Rule::requiredIf($this->isScheduled()),
                'nullable',
                'date_format:H:i',
            ],
            'cod_address' => [
                Rule::requiredIf($this->input('shipping_method') === ShippingMethod::Cod->value),
                'nullable',
                'string',
                'min:10',
                'max:500',
            ],

            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    private function isScheduled(): bool
    {
        return ShippingMethod::tryFrom((string) $this->input('shipping_method'))?->isScheduled() ?? false;
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'recipient_name' => 'nama penerima',
            'recipient_phone' => 'nomor telepon',
            'shipping_city' => 'kota tujuan',
            'shipping_address' => 'alamat lengkap',
            'shipping_method' => 'metode pengiriman',
            'payment_method' => 'metode pembayaran',
            'booking_date' => 'tanggal jadwal',
            'booking_time' => 'jam jadwal',
            'cod_address' => 'alamat COD',
        ];
    }
}
