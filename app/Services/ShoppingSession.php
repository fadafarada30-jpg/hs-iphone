<?php

namespace App\Services;

use App\Models\Listing;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

class ShoppingSession
{
    private const CART_KEY = 'cart';

    private const WISHLIST_KEY = 'wishlist';

    /**
     * Return all cart items as a Collection of listing IDs.
     *
     * @return Collection<int, int>
     */
    public function cartIds(): Collection
    {
        return collect(Session::get(self::CART_KEY, []));
    }

    /**
     * @return Collection<int, Listing>
     */
    public function cartListings(): Collection
    {
        $ids = $this->cartIds();

        if ($ids->isEmpty()) {
            return collect();
        }

        return Listing::with(['seller', 'photos'])
            ->whereIn('id', $ids)
            ->where('status', 'active')
            ->get();
    }

    public function addToCart(int $listingId): void
    {
        $ids = $this->cartIds()->push($listingId)->unique()->values()->all();
        Session::put(self::CART_KEY, $ids);
    }

    public function removeFromCart(int $listingId): void
    {
        $ids = $this->cartIds()->reject(fn (int $id) => $id === $listingId)->values()->all();
        Session::put(self::CART_KEY, $ids);
    }

    public function clearCart(): void
    {
        Session::forget(self::CART_KEY);
    }

    public function isInCart(int $listingId): bool
    {
        return $this->cartIds()->contains($listingId);
    }

    public function cartCount(): int
    {
        return $this->cartIds()->count();
    }

    /**
     * @return Collection<int, int>
     */
    public function wishlistIds(): Collection
    {
        return collect(Session::get(self::WISHLIST_KEY, []));
    }

    /**
     * @return Collection<int, Listing>
     */
    public function wishlistListings(): Collection
    {
        $ids = $this->wishlistIds();

        if ($ids->isEmpty()) {
            return collect();
        }

        return Listing::with(['seller', 'photos'])
            ->whereIn('id', $ids)
            ->where('status', 'active')
            ->get();
    }

    public function toggleWishlist(int $listingId): bool
    {
        $ids = $this->wishlistIds();

        if ($ids->contains($listingId)) {
            Session::put(self::WISHLIST_KEY, $ids->reject(fn (int $id) => $id === $listingId)->values()->all());

            return false;
        }

        Session::put(self::WISHLIST_KEY, $ids->push($listingId)->unique()->values()->all());

        return true;
    }

    public function isWishlisted(int $listingId): bool
    {
        return $this->wishlistIds()->contains($listingId);
    }

    public function wishlistCount(): int
    {
        return $this->wishlistIds()->count();
    }
}
