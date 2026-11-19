<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $featured = Listing::with(['seller', 'photos'])
            ->active()
            ->featured()
            ->latestFirst()
            ->take(6)
            ->get();

        $latest = Listing::with(['seller', 'photos'])
            ->active()
            ->latestFirst()
            ->take(12)
            ->get();

        $seriesCounts = Listing::active()
            ->selectRaw('series, count(*) as total')
            ->groupBy('series')
            ->orderBy('series', 'desc')
            ->pluck('total', 'series');

        return view('home', compact('featured', 'latest', 'seriesCounts'));
    }
}
