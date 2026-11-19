<?php

namespace App\Http\Controllers;

use App\Enums\ListingCondition;
use App\Enums\ListingStatus;
use App\Enums\Warranty;
use App\Http\Requests\StoreListingRequest;
use App\Models\Listing;
use App\Models\ListingPhoto;
use App\Models\Order;
use App\Services\ShoppingSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ListingController extends Controller
{
    public function index(Request $request): View
    {
        $query = Listing::with(['seller', 'photos'])->active()->latestFirst();

        if ($request->filled('seri')) {
            $query->where('series', $request->string('seri'));
        }

        if ($request->filled('kondisi')) {
            $query->where('condition', $request->string('kondisi'));
        }

        if ($request->filled('garansi')) {
            $query->where('warranty', $request->string('garansi'));
        }

        if ($request->filled('min_harga')) {
            $query->where('price', '>=', (int) $request->input('min_harga'));
        }

        if ($request->filled('max_harga')) {
            $query->where('price', '<=', (int) $request->input('max_harga'));
        }

        if ($request->filled('cari')) {
            $search = $request->string('cari');
            $query->where(fn ($q) => $q->where('model', 'like', "%{$search}%")
                ->orWhere('color', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%"));
        }

        $sortable = ['harga_asc' => 'price', 'harga_desc' => 'price', 'terbaru' => 'created_at'];
        $sortKey = $request->input('urut', 'terbaru');
        if (isset($sortable[$sortKey])) {
            $dir = str_ends_with((string) $sortKey, '_asc') ? 'asc' : 'desc';
            $query->reorder($sortable[$sortKey], $dir);
        }

        $listings = $query->paginate(16)->withQueryString();

        return view('listings.index', [
            'listings' => $listings,
            'conditions' => ListingCondition::cases(),
            'warranties' => Warranty::cases(),
            'series' => ['11', '12', '13', '14', '15', '16'],
            'filters' => $request->only(['seri', 'kondisi', 'garansi', 'min_harga', 'max_harga', 'cari', 'urut']),
        ]);
    }

    public function show(Listing $listing, ShoppingSession $shopping): View
    {
        $listing->increment('views');
        $listing->load(['seller.reviewsReceived', 'photos', 'reviews.reviewer']);

        $related = Listing::with(['seller', 'photos'])
            ->active()
            ->where('series', $listing->series)
            ->where('id', '!=', $listing->id)
            ->latestFirst()
            ->take(4)
            ->get();

        $bookings = Order::notCancelled()
            ->whereHas('items', fn ($query) => $query->where('listing_id', $listing->id))
            ->whereNotNull('booking_date')
            ->with('user')
            ->orderBy('booking_date')
            ->orderBy('booking_time')
            ->get(['id', 'user_id', 'shipping_method', 'booking_date', 'booking_time']);

        return view('listings.show', [
            'listing' => $listing,
            'related' => $related,
            'bookings' => $bookings,
            'isWishlisted' => $shopping->isWishlisted($listing->id),
            'isInCart' => $shopping->isInCart($listing->id),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Listing::class);

        return view('listings.create', [
            'conditions' => ListingCondition::cases(),
            'warranties' => Warranty::cases(),
        ]);
    }

    public function store(StoreListingRequest $request): RedirectResponse
    {
        $data = $request->validated();
        unset($data['photos']);

        $data['seller_id'] = $request->user()->id;
        $data['slug'] = Str::slug("{$data['model']} {$data['storage_gb']}gb").'-'.Str::random(6);
        $data['status'] = ListingStatus::Active;
        $data['completeness'] = $data['completeness'] ?? [];

        $listing = Listing::create($data);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $index => $file) {
                $path = $file->store("listings/{$listing->id}", 'public');
                ListingPhoto::create([
                    'listing_id' => $listing->id,
                    'path' => $path,
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()->route('listings.show', $listing)
            ->with('success', 'Listing berhasil dipublikasikan!');
    }
}
