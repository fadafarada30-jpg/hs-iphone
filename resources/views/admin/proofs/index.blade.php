<x-admin-layout title="Bukti sold">
    <div class="admin-page-head">
        <div>
            <p class="admin-kicker">Operasional / Bukti Sold</p>
            <h1 class="admin-title">Review bukti sold.</h1>
            <p class="admin-subtitle">Verifikasi bukti penjualan dari driver. Persetujuan akan menandai pesanan selesai dan unit menjadi SOLD.</p>
        </div>
        <div class="admin-actions">
            <a href="{{ route('admin.dashboard') }}" class="admin-btn admin-btn--ghost admin-btn--sm">Dashboard</a>
        </div>
    </div>

    <div class="admin-stats admin-stats--three">
        <div class="admin-stat">
            <p class="admin-stat-num">{{ number_format($counts['pending']) }}</p>
            <p class="admin-stat-label">Menunggu review</p>
            <p class="admin-stat-foot">Bukti baru dari driver</p>
        </div>
        <div class="admin-stat">
            <p class="admin-stat-num">{{ number_format($counts['approved']) }}</p>
            <p class="admin-stat-label">Disetujui</p>
            <p class="admin-stat-foot">Unit berstatus SOLD</p>
        </div>
        <div class="admin-stat">
            <p class="admin-stat-num">{{ number_format($counts['rejected']) }}</p>
            <p class="admin-stat-label">Ditolak</p>
            <p class="admin-stat-foot">Driver bisa unggah ulang</p>
        </div>
    </div>

    <p class="admin-section-label">Menunggu review ({{ $pending->count() }})</p>

    @if($pending->isEmpty())
        <div class="admin-empty">Tidak ada bukti sold yang menunggu review saat ini.</div>
    @else
        <div class="admin-card-grid">
            @foreach($pending as $order)
                <article class="admin-card">
                    <div class="admin-card-head">
                        <div>
                            <div class="admin-card-title">{{ $order->code }}</div>
                            <div class="admin-card-meta">{{ $order->user->name }} &middot; driver {{ $order->driver?->name ?? 'Belum tersedia' }} &middot; {{ $order->booking_date?->translatedFormat('d M Y') }} {{ $order->booking_time }}</div>
                        </div>
                        <span class="admin-pill">Menunggu review</span>
                    </div>

                    <div class="admin-card-body">
                        <div class="admin-meta-grid">
                            <div>
                                <p class="admin-meta-label">Unit</p>
                                <p class="admin-meta-value">{{ $order->items->map(fn ($item) => $item->model.' '.$item->storageLabel())->join(', ') }}</p>
                            </div>
                            <div>
                                <p class="admin-meta-label">Total pesanan</p>
                                <p class="admin-meta-value">Rp {{ number_format($order->total, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="admin-meta-label">Keterangan driver</p>
                                <p class="admin-meta-value">{{ $order->sold_proof_notes ?: 'Tidak ada catatan' }}</p>
                            </div>
                            <div>
                                <p class="admin-meta-label">Driver berangkat</p>
                                <p class="admin-meta-value">{{ $order->departed_at ? $order->departed_at->translatedFormat('d M Y H:i') : 'Belum tercatat' }}</p>
                            </div>
                        </div>

                        @if($order->departure_notes)
                            <p class="admin-card-desc">Catatan keberangkatan: {{ $order->departure_notes }}</p>
                        @endif

                        <div class="admin-photo-links">
                            <a href="{{ asset('storage/'.$order->sold_proof_photo) }}" target="_blank" rel="noopener" class="admin-photo-link">Bukti sold</a>
                            @if($order->departure_photo_self)
                                <a href="{{ asset('storage/'.$order->departure_photo_self) }}" target="_blank" rel="noopener" class="admin-photo-link">Foto driver</a>
                            @endif
                            @if($order->departure_photo_location)
                                <a href="{{ asset('storage/'.$order->departure_photo_location) }}" target="_blank" rel="noopener" class="admin-photo-link">Foto lokasi</a>
                            @endif
                        </div>

                        <div class="admin-card-actions">
                            <form action="{{ route('admin.proofs.approve', $order) }}" method="POST" class="admin-action-form" onsubmit="return confirm('Setujui bukti sold ini?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="admin-btn admin-btn--sm">Setujui &amp; tandai SOLD</button>
                            </form>
                            <form action="{{ route('admin.proofs.reject', $order) }}" method="POST" class="admin-inline-form">
                                @csrf
                                @method('PATCH')
                                <input type="text" name="sold_proof_rejected_note" class="input" placeholder="Alasan penolakan" required>
                                <button type="submit" class="admin-btn admin-btn--danger admin-btn--sm">Tolak</button>
                            </form>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif

    <p class="admin-section-label">Riwayat terbaru</p>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Pesanan</th>
                    <th>Customer</th>
                    <th>Driver</th>
                    <th class="center">Status bukti</th>
                    <th>Direview</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recent as $order)
                    <tr>
                        <td>
                            <div class="admin-cell-title">{{ $order->code }}</div>
                            <div class="admin-cell-meta">{{ $order->created_at->translatedFormat('d M Y') }}</div>
                        </td>
                        <td>{{ $order->user->name }}</td>
                        <td>{{ $order->driver?->name ?? 'Belum tersedia' }}</td>
                        <td class="center">
                            <span class="admin-pill {{ $order->sold_proof_status->value === 'approved' ? 'is-strong' : '' }} {{ $order->sold_proof_status->value === 'rejected' ? 'is-danger' : '' }} {{ $order->sold_proof_status->value === 'pending' ? 'is-quiet' : '' }}">
                                {{ $order->sold_proof_status->label() }}
                            </span>
                        </td>
                        <td>{{ $order->sold_proof_reviewed_at?->translatedFormat('d M Y H:i') ?? 'Belum diverifikasi' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="admin-cell-meta" style="padding:20px; text-align:center;">Belum ada riwayat review bukti sold.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>
