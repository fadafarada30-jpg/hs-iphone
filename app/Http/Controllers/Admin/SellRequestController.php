<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Enums\SellRequestStatus;
use App\Http\Controllers\Controller;
use App\Models\ChatThread;
use App\Models\Message;
use App\Models\SellRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SellRequestController extends Controller
{
    public function index(Request $request): View
    {
        $requests = SellRequest::with(['user', 'reviewer', 'listing'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $pendingCount = SellRequest::pending()->count();

        return view('admin.sell-requests.index', [
            'requests' => $requests,
            'pendingCount' => $pendingCount,
            'statuses' => SellRequestStatus::cases(),
            'counts' => [
                'pending' => $pendingCount,
                'approved' => SellRequest::where('status', SellRequestStatus::Approved)->count(),
                'rejected' => SellRequest::where('status', SellRequestStatus::Rejected)->count(),
            ],
        ]);
    }

    public function approve(SellRequest $sellRequest): RedirectResponse
    {
        abort_unless($sellRequest->status === SellRequestStatus::Pending, 422, 'Pengajuan sudah diproses.');

        $admin = auth()->user()?->isAdmin() ? auth()->user() : User::where('role', Role::Admin)->firstOrFail();

        // Disetujui = lanjut negosiasi di chat. Listing belum dipublikasikan ke katalog.
        $thread = ChatThread::create([
            'listing_id' => null,
            'buyer_id' => $sellRequest->user_id,
            'seller_id' => $admin->id,
            'unread_count' => 1,
            'last_message_at' => now(),
        ]);

        Message::create([
            'chat_thread_id' => $thread->id,
            'sender_id' => $admin->id,
            'body' => "Halo! Pengajuan jual iPhone {$sellRequest->model} {$sellRequest->storage_gb}GB kamu sudah disetujui HS Depok. Yuk lanjutkan detail harga dan penyerahan unit di chat ini.",
        ]);

        $sellRequest->update([
            'status' => SellRequestStatus::Approved,
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
            'chat_thread_id' => $thread->id,
        ]);

        return redirect()->route('chat.show', $thread)
            ->with('success', 'Pengajuan disetujui. Chat dengan pemilik HP sudah dibuka.');
    }

    public function reject(Request $request, SellRequest $sellRequest): RedirectResponse
    {
        abort_unless($sellRequest->status === SellRequestStatus::Pending, 422, 'Pengajuan sudah diproses.');

        $data = $request->validate([
            'rejection_note' => ['required', 'string', 'max:500'],
        ], [
            'rejection_note.required' => 'Alasan penolakan wajib diisi.',
        ]);

        $admin = auth()->user()?->isAdmin() ? auth()->user() : User::where('role', Role::Admin)->firstOrFail();

        $thread = ChatThread::create([
            'listing_id' => null,
            'buyer_id' => $sellRequest->user_id,
            'seller_id' => $admin->id,
            'unread_count' => 1,
            'last_message_at' => now(),
        ]);

        Message::create([
            'chat_thread_id' => $thread->id,
            'sender_id' => $admin->id,
            'body' => "Pengajuan jual iPhone {$sellRequest->model} {$sellRequest->storage_gb}GB belum bisa kami terima. Alasan: {$data['rejection_note']}. Silakan perbaiki spesifikasi atau hubungi HS Depok jika ada pertanyaan.",
        ]);

        $sellRequest->update([
            'status' => SellRequestStatus::Rejected,
            'rejection_note' => $data['rejection_note'],
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
            'chat_thread_id' => $thread->id,
        ]);

        return back()->with('success', 'Pengajuan ditolak. Alasan sudah dikirim ke chat pemilik HP.');
    }
}
