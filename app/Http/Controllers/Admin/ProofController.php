<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ListingStatus;
use App\Enums\OrderStatus;
use App\Enums\ProofStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\BookingNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProofController extends Controller
{
    public function __construct(private readonly BookingNotifier $notifier) {}

    public function index(): View
    {
        $pending = Order::pendingProof()
            ->with(['user', 'driver', 'items.listing'])
            ->latest('updated_at')
            ->get();

        $recent = Order::where('sold_proof_status', '!=', ProofStatus::None)
            ->with(['user', 'driver', 'items.listing'])
            ->latest('sold_proof_reviewed_at')
            ->take(20)
            ->get();

        return view('admin.proofs.index', [
            'pending' => $pending,
            'recent' => $recent,
            'counts' => [
                'pending' => $pending->count(),
                'approved' => Order::where('sold_proof_status', ProofStatus::Approved)->count(),
                'rejected' => Order::where('sold_proof_status', ProofStatus::Rejected)->count(),
            ],
        ]);
    }

    public function approve(Order $order): RedirectResponse
    {
        $this->authorize('advance', $order);

        abort_unless($order->sold_proof_status === ProofStatus::Pending, 422, 'Bukti sold belum menunggu review.');

        $order->update([
            'sold_proof_status' => ProofStatus::Approved,
            'sold_proof_reviewed_by' => auth()->id(),
            'sold_proof_reviewed_at' => now(),
            'status' => OrderStatus::Completed,
        ]);

        $order->items->each(function ($item) {
            $item->listing?->update(['status' => ListingStatus::Sold]);
        });

        $order->items->pluck('listing')->filter()->unique('id')
            ->each(fn ($listing) => $this->notifier->notifySold($listing, auth()->user()));

        return back()->with('success', 'Bukti sold disetujui. Status produk berubah menjadi SOLD dan customer diberi notifikasi.');
    }

    public function reject(Request $request, Order $order): RedirectResponse
    {
        $this->authorize('advance', $order);

        abort_unless($order->sold_proof_status === ProofStatus::Pending, 422, 'Bukti sold belum menunggu review.');

        $data = $request->validate([
            'sold_proof_rejected_note' => ['required', 'string', 'max:500'],
        ], [
            'sold_proof_rejected_note.required' => 'Alasan penolakan wajib diisi.',
        ]);

        $order->update([
            'sold_proof_status' => ProofStatus::Rejected,
            'sold_proof_rejected_note' => $data['sold_proof_rejected_note'],
            'sold_proof_reviewed_by' => auth()->id(),
            'sold_proof_reviewed_at' => now(),
        ]);

        return back()->with('success', 'Bukti sold ditolak. Driver bisa mengunggah ulang.');
    }
}
