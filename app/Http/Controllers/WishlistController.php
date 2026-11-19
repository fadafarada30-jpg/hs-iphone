<?php

namespace App\Http\Controllers;

use App\Services\ShoppingSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function __construct(private readonly ShoppingSession $shopping) {}

    public function index(): View
    {
        return view('wishlist', ['listings' => $this->shopping->wishlistListings()]);
    }

    public function toggle(Request $request, int $listing): JsonResponse|RedirectResponse
    {
        $added = $this->shopping->toggleWishlist($listing);

        if ($request->wantsJson()) {
            return response()->json([
                'added' => $added,
                'count' => $this->shopping->wishlistCount(),
            ]);
        }

        return back()->with('success', $added ? 'Ditambahkan ke wishlist.' : 'Dihapus dari wishlist.');
    }
}
