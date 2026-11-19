<?php

namespace App\Http\Controllers;

use App\Enums\ListingCondition;
use App\Http\Requests\StoreSellRequestRequest;
use App\Models\SellRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SellRequestController extends Controller
{
    public function index(): View
    {
        $requests = SellRequest::where('user_id', auth()->id())
            ->latest()
            ->with(['listing', 'chatThread'])
            ->get();

        return view('sell.index', ['requests' => $requests]);
    }

    public function create(): View
    {
        return view('sell.create', [
            'conditions' => ListingCondition::cases(),
        ]);
    }

    public function store(StoreSellRequestRequest $request): RedirectResponse
    {
        $photos = [];

        foreach ($request->file('photos', []) as $index => $file) {
            $photos[] = $file->store("sell-requests/{$request->user()->id}", 'public');
        }

        SellRequest::create([
            'user_id' => $request->user()->id,
            'model' => $request->validated('model'),
            'series' => $request->validated('series'),
            'storage_gb' => $request->validated('storage_gb'),
            'color' => $request->validated('color'),
            'condition' => $request->validated('condition'),
            'battery_health' => $request->validated('battery_health'),
            'price' => $request->validated('price'),
            'description' => $request->validated('description'),
            'city' => $request->validated('city'),
            'photos' => $photos,
        ]);

        return redirect()->route('sell.index')
            ->with('success', 'Pengajuan jual terkirim. Admin HS Depok akan meninjau spesifikasi HP kamu.');
    }
}
