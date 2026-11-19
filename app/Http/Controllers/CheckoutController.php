<?php

namespace App\Http\Controllers;

use App\Actions\CreateOrder;
use App\Enums\PaymentMethod;
use App\Enums\ShippingMethod;
use App\Http\Requests\StoreOrderRequest;
use App\Services\ShoppingSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(private readonly ShoppingSession $shopping) {}

    public function index(): View|RedirectResponse
    {
        $listings = $this->shopping->cartListings();

        if ($listings->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Keranjang kosong.');
        }

        return view('checkout', [
            'listings' => $listings,
            'shippingMethods' => ShippingMethod::cases(),
            'paymentMethods' => PaymentMethod::cases(),
        ]);
    }

    public function store(StoreOrderRequest $request, CreateOrder $action): RedirectResponse
    {
        $order = $action->handle($request);

        return redirect()->route('orders.show', $order)
            ->with('success', 'Pesanan berhasil dibuat! Kode pesanan: '.$order->code);
    }
}
