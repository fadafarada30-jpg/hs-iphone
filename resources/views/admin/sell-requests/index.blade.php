<x-admin-layout title="Pengajuan jual">
    <div class="admin-page-head">
        <div>
            <p class="admin-kicker">Transaksi / Pengajuan Jual</p>
            <h1 class="admin-title">Pengajuan jual.</h1>
            <p class="admin-subtitle">Tinjau spesifikasi HP yang diajukan pelanggan. Persetujuan akan membuka percakapan dengan pemilik unit.</p>
        </div>
        <div class="admin-actions">
            <a href="{{ route('admin.dashboard') }}" class="admin-btn admin-btn--ghost admin-btn--sm">Dashboard</a>
        </div>
    </div>

    <div class="admin-stats admin-stats--three">
        <div class="admin-stat">
            <p class="admin-stat-num">{{ number_format($counts['pending']) }}</p>
            <p class="admin-stat-label">Menunggu review</p>
            <p class="admin-stat-foot">Perlu ditindaklanjuti</p>
        </div>
        <div class="admin-stat">
            <p class="admin-stat-num">{{ number_format($counts['approved']) }}</p>
            <p class="admin-stat-label">Disetujui</p>
            <p class="admin-stat-foot">Chat sudah dibuka</p>
        </div>
        <div class="admin-stat">
            <p class="admin-stat-num">{{ number_format($counts['rejected']) }}</p>
            <p class="admin-stat-label">Ditolak</p>
            <p class="admin-stat-foot">Alasan sudah dikirim</p>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.sell-requests') }}" class="admin-filter">
        <select name="status" class="input">
            <option value="">Semua status</option>
            @foreach($statuses as $status)
                <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>
        <button type="submit" class="admin-btn admin-btn--ghost admin-btn--sm">Filter</button>
        <span class="admin-filter-spacer"></span>
        <span class="admin-filter-count">{{ $requests->total() }} pengajuan ditemukan</span>
    </form>

    @if($requests->isEmpty())
        <div class="admin-empty">Belum ada pengajuan jual yang cocok dengan filter ini.</div>
    @else
        <div class="admin-card-grid">
            @foreach($requests as $request)
                <article class="admin-card">
                    <div class="admin-card-head">
                        <div>
                            <div class="admin-card-title">{{ $request->model }} &middot; {{ $request->storageLabel() }}</div>
                            <div class="admin-card-meta">{{ $request->user->name }} &middot; {{ $request->city }} &middot; {{ $request->created_at->translatedFormat('d M Y H:i') }}</div>
                        </div>
                        <span class="admin-pill {{ $request->status->value === 'approved' ? 'is-strong' : '' }} {{ $request->status->value === 'rejected' ? 'is-danger' : '' }} {{ $request->status->value === 'pending' ? 'is-quiet' : '' }}">
                            {{ $request->status->label() }}
                        </span>
                    </div>

                    <div class="admin-card-body">
                        <div class="admin-meta-grid">
                            <div>
                                <p class="admin-meta-label">Warna</p>
                                <p class="admin-meta-value">{{ $request->color }}</p>
                            </div>
                            <div>
                                <p class="admin-meta-label">Kondisi</p>
                                <p class="admin-meta-value">{{ $request->condition->label() }} @if($request->battery_health) &middot; BH {{ $request->battery_health }}% @endif</p>
                            </div>
                            <div>
                                <p class="admin-meta-label">Harga harapan</p>
                                <p class="admin-meta-value">Rp {{ number_format($request->price, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="admin-meta-label">Status chat</p>
                                <p class="admin-meta-value">{{ $request->chat_thread_id ? 'Percakapan tersedia' : 'Belum dibuka' }}</p>
                            </div>
                        </div>

                        @if($request->description)
                            <p class="admin-card-desc">{{ $request->description }}</p>
                        @endif

                        @if($request->photos)
                            <div class="admin-photo-links">
                                @foreach($request->photos as $index => $path)
                                    <a href="{{ asset('storage/'.$path) }}" target="_blank" rel="noopener" class="admin-photo-link">Foto {{ $loop->iteration }}</a>
                                @endforeach
                            </div>
                        @endif

                        @if($request->status === \App\Enums\SellRequestStatus::Rejected && $request->rejection_note)
                            <p class="admin-card-desc" style="color:#b42318;">Alasan: {{ $request->rejection_note }}</p>
                        @endif

                        <div class="admin-card-actions">
                            @if($request->status === \App\Enums\SellRequestStatus::Pending)
                                <form action="{{ route('admin.sell-requests.approve', $request) }}" method="POST" class="admin-action-form" onsubmit="return confirm('Setujui pengajuan ini?')">
                                    @csrf
                                    <button type="submit" class="admin-btn admin-btn--sm">Setujui &amp; chat</button>
                                </form>
                                <form action="{{ route('admin.sell-requests.reject', $request) }}" method="POST" class="admin-inline-form">
                                    @csrf
                                    <input type="text" name="rejection_note" class="input" placeholder="Alasan penolakan" required>
                                    <button type="submit" class="admin-btn admin-btn--danger admin-btn--sm">Tolak</button>
                                </form>
                            @elseif($request->chat_thread_id)
                                <a href="{{ route('chat.show', $request->chat_thread_id) }}" class="admin-btn admin-btn--ghost admin-btn--sm">Lihat chat</a>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="admin-filter" style="margin-top:18px; margin-bottom:0;">
            <span class="admin-filter-spacer"></span>
            {{ $requests->links() }}
        </div>
    @endif
</x-admin-layout>
