<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Enums\ProofStatus;
use App\Enums\ShippingMethod;
use App\Http\Requests\StoreDepartureRequest;
use App\Http\Requests\StoreSoldProofRequest;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DriverController extends Controller
{
    public function index(): View
    {
        $orders = Order::forDriver(auth()->id())
            ->where('shipping_method', ShippingMethod::Cod)
            ->whereNotIn('status', [OrderStatus::Completed, OrderStatus::Cancelled])
            ->with(['user', 'items.listing'])
            ->orderBy('booking_date')
            ->orderBy('booking_time')
            ->get();

        return view('driver.index', ['orders' => $orders]);
    }

    public function show(Order $order): View
    {
        abort_unless($order->driver_id === auth()->id(), 403);

        $order->load(['user', 'items.listing']);

        return view('driver.show', ['order' => $order]);
    }

    public function depart(StoreDepartureRequest $request, Order $order): RedirectResponse
    {
        abort_unless($order->driver_id === auth()->id(), 403);

        $self = $request->file('departure_photo_self')->store(
            "driver/orders/{$order->id}/departure",
            'public'
        );
        $location = $request->file('departure_photo_location')->store(
            "driver/orders/{$order->id}/departure",
            'public'
        );

        $order->update([
            'departed_at' => now(),
            'departure_photo_self' => $self,
            'departure_photo_location' => $location,
            'departure_notes' => $request->validated('departure_notes'),
            'status' => $order->status === OrderStatus::Processing
                ? OrderStatus::Shipped
                : $order->status,
        ]);

        return back()->with('success', 'Absen berangkat berhasil dikirim ke admin.');
    }

    public function uploadProof(StoreSoldProofRequest $request, Order $order): RedirectResponse
    {
        abort_unless($order->driver_id === auth()->id(), 403);

        $path = $request->file('sold_proof_photo')->store(
            "driver/orders/{$order->id}/sold-proof",
            'public'
        );

        $order->update([
            'sold_proof_photo' => $path,
            'sold_proof_notes' => $request->validated('sold_proof_notes'),
            'sold_proof_status' => ProofStatus::Pending,
        ]);

        return back()->with('success', 'Bukti sold dikirim. Menunggu persetujuan admin.');
    }
}
