<?php

namespace App\Http\Controllers;

use App\Services\ShoppingSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(private readonly ShoppingSession $shopping) {}

    public function index(): View
    {
        return view('cart', ['listings' => $this->shopping->cartListings()]);
    }

    public function add(Request $request, int $listing): JsonResponse|RedirectResponse
    {
        $this->shopping->addToCart($listing);

        if ($request->wantsJson()) {
            return response()->json(['count' => $this->shopping->cartCount()]);
        }

        return back()->with('success', 'Ditambahkan ke keranjang.');
    }

    public function remove(int $listing): RedirectResponse
    {
        $this->shopping->removeFromCart($listing);

        return back()->with('success', 'Dihapus dari keranjang.');
    }
}
