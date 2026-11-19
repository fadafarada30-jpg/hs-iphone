<?php

namespace App\Http\Controllers;

use App\Models\SellRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $user->load(['listings' => fn ($q) => $q->with('photos')->latestFirst()->take(5)]);

        $recentOrders = $user->orders()
            ->with('items')
            ->latestFirst()
            ->take(5)
            ->get();

        $reviews = $user->reviewsReceived()
            ->with('reviewer')
            ->latestFirst()
            ->take(5)
            ->get();

        $stats = [
            'orders' => $user->orders()->count(),
            'requests' => SellRequest::where('user_id', $user->id)->count(),
            'reviews' => (int) $user->reviews_count,
        ];

        return view('account', compact('user', 'recentOrders', 'reviews', 'stats'));
    }
}
