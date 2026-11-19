<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Order;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'totalListings' => Listing::count(),
            'activeListings' => Listing::active()->count(),
            'totalOrders' => Order::count(),
            'totalUsers' => User::count(),
            'recentOrders' => Order::with(['user', 'items'])->latestFirst()->take(8)->get(),
            'recentListings' => Listing::with('seller')->latestFirst()->take(8)->get(),
        ]);
    }
}
