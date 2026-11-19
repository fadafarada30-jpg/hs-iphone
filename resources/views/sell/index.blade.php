<x-layout title="Pengajuan Jual Saya">
    <div class="sell-stage">
        <div class="ed-wrap">
            <div class="sell-list-head">
                <div>
                    <p class="sell-kicker">Pengajuan Jual</p>
                    <h1 class="sell-title">Riwayat pengajuan kamu.</h1>
                </div>
                <a href="{{ route('sell.create') }}" class="sell-submit">Ajukan iPhone baru</a>
            </div>

            @if($requests->isEmpty())
                <div class="sell-empty">
                    <p class="sell-empty-mark">Belum ada aktivitas</p>
                    <h3>Belum ada pengajuan.</h3>
                    <p>Punya iPhone yang mau dijual? Kirim spesifikasinya dan biarkan tim kami meninjau unit kamu.</p>
                    <a href="{{ route('sell.create') }}" class="sell-submit">Buat pengajuan pertama</a>
                </div>
            @else
                <div class="sell-list">
                    @foreach($requests as $request)
                        <article class="sell-item">
                            <span class="sell-item-num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>

                            <div>
                                <h3>{{ $request->model }}</h3>
                                <p class="sell-item-spec">{{ $request->storageLabel() }} · {{ $request->color }} · {{ $request->condition->label() }}{{ $request->battery_health ? ' · BH '.$request->battery_health.'%' : '' }}</p>
                                <p class="sell-item-meta">Diajukan {{ $request->created_at->translatedFormat('d M Y H:i') }} · Harga harapan Rp {{ number_format($request->price, 0, ',', '.') }}</p>
                            </div>

                            <span class="sell-status {{ $request->status === \App\Enums\SellRequestStatus::Approved ? 'is-approved' : '' }} {{ $request->status === \App\Enums\SellRequestStatus::Rejected ? 'is-rejected' : '' }}">{{ $request->status->label() }}</span>

                            @if($request->status === \App\Enums\SellRequestStatus::Rejected && $request->rejection_note)
                                <div class="sell-note-box">
                                    <strong>Alasan penolakan</strong>
                                    {{ $request->rejection_note }}
                                </div>
                            @endif

                            @if($request->chat_thread_id || ($request->listing_id && $request->listing))
                                <div class="sell-actions">
                                    @if($request->chat_thread_id)
                                        <a href="{{ route('chat.show', $request->chat_thread_id) }}">Lihat chat dengan admin</a>
                                    @endif
                                    @if($request->listing_id && $request->listing)
                                        <a href="{{ route('listings.show', $request->listing) }}">Lihat listing</a>
                                    @endif
                                </div>
                            @endif
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-layout>
