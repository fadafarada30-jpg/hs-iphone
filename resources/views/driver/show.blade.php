<x-driver-layout title="Detail jadwal COD">
    <div class="driver-page-head">
        <div>
            <p class="driver-kicker">Driver COD / Detail jadwal</p>
            <h1 class="driver-title">{{ $order->code }}</h1>
            <p class="driver-subtitle">Detail penugasan, absen keberangkatan, dan dokumentasi penyerahan unit untuk pesanan ini.</p>
        </div>
        <div class="driver-page-actions">
            <a href="{{ route('driver.index') }}" class="driver-btn driver-btn--ghost">&larr; Kembali ke jadwal</a>
            @if($order->recipient_phone)
                <a href="{{ $order->recipientPhoneLink() }}" class="driver-btn">Hubungi customer</a>
            @endif
        </div>
    </div>

    <section class="driver-block">
        <div class="driver-block-head">
            <div>
                <h2 class="driver-block-title">Informasi penugasan</h2>
                <p class="driver-block-note">Jadwal dan alamat penyerahan unit.</p>
            </div>
            <span class="badge badge-{{ $order->status->tone() }}">{{ $order->status->label() }}</span>
        </div>

        <div class="driver-detail-grid">
            <div>
                <p class="driver-detail-label">Tanggal &amp; jam</p>
                <p class="driver-detail-value">{{ $order->booking_date?->translatedFormat('d M Y') }} · {{ $order->booking_time }} WIB</p>
            </div>
            <div>
                <p class="driver-detail-label">Customer</p>
                <p class="driver-detail-value">{{ $order->user->name }}</p>
                @if($order->recipient_phone)
                    <a href="{{ $order->recipientPhoneLink() }}" class="driver-phone-link">{{ $order->recipient_phone }} &middot; Hubungi</a>
                @endif
            </div>
            <div class="driver-field full">
                <p class="driver-detail-label">Alamat COD</p>
                <p class="driver-detail-value">{{ $order->cod_address }}</p>
            </div>
            <div class="driver-field full">
                <p class="driver-detail-label">Unit</p>
                <p class="driver-detail-value">
                    {{ $order->items->map(fn ($item) => $item->model.' '.$item->storage_gb.'GB '.$item->color)->join(', ') }}
                </p>
            </div>
            @if($order->notes)
                <div class="driver-field full">
                    <p class="driver-detail-label">Catatan customer</p>
                    <p class="driver-detail-value">{{ $order->notes }}</p>
                </div>
            @endif
        </div>
    </section>

    <section class="driver-block">
        <div class="driver-block-head">
            <div>
                <h2 class="driver-block-title">Absen berangkat</h2>
                <p class="driver-block-note">Jam tercatat otomatis saat kamu mengirim. Foto menjadi bukti untuk admin.</p>
            </div>
        </div>

        @if($order->departed_at)
            <div class="driver-note">
                <p class="font-semibold">Sudah berangkat {{ $order->departed_at->translatedFormat('d M Y H:i') }} WIB</p>
                <p class="mt-1">{{ $order->departure_notes }}</p>
                <div class="flex gap-3 mt-3">
                    <a href="{{ asset('storage/'.$order->departure_photo_self) }}" target="_blank" rel="noopener" class="text-sm underline">Foto selfie/barang</a>
                    <a href="{{ asset('storage/'.$order->departure_photo_location) }}" target="_blank" rel="noopener" class="text-sm underline">Foto lokasi</a>
                </div>
            </div>
        @endif

        <form action="{{ route('driver.depart', $order) }}" method="POST" enctype="multipart/form-data" class="driver-form-grid">
            @csrf
            <div class="driver-field">
                <label for="departure_photo_self">Foto selfie / barang</label>
                <input id="departure_photo_self" type="file" name="departure_photo_self" accept="image/*" class="input" required>
                @error('departure_photo_self') <p class="text-xs mt-1" style="color:var(--color-danger)">{{ $message }}</p> @enderror
            </div>
            <div class="driver-field">
                <label for="departure_photo_location">Foto lokasi / alamat</label>
                <input id="departure_photo_location" type="file" name="departure_photo_location" accept="image/*" class="input" required>
                @error('departure_photo_location') <p class="text-xs mt-1" style="color:var(--color-danger)">{{ $message }}</p> @enderror
            </div>
            <div class="driver-field full">
                <label for="departure_notes">Keterangan</label>
                <textarea id="departure_notes" name="departure_notes" rows="3" class="input" placeholder="Contoh: berangkat dari toko, estimasi tiba 20 menit" required>{{ old('departure_notes') }}</textarea>
                @error('departure_notes') <p class="text-xs mt-1" style="color:var(--color-danger)">{{ $message }}</p> @enderror
            </div>
            <div class="driver-field full driver-form-foot">
                <p class="driver-hint">Data ini hanya terlihat oleh admin sebagai bagian dari alur COD.</p>
                <button class="driver-btn">Kirim absen berangkat</button>
            </div>
        </form>
    </section>

    <section class="driver-block">
        <div class="driver-block-head">
            <div>
                <h2 class="driver-block-title">Bukti sold</h2>
                <p class="driver-block-note">Unggah dokumentasi setelah unit diserahkan. Admin yang menentukan status SOLD.</p>
            </div>
            <span class="badge badge-{{ $order->sold_proof_status->tone() }}">{{ $order->sold_proof_status->label() }}</span>
        </div>

        @if($order->sold_proof_status === \App\Enums\ProofStatus::Rejected && $order->sold_proof_rejected_note)
            <div class="driver-note driver-note--danger">
                <p class="font-semibold">Bukti ditolak admin</p>
                <p class="mt-1">{{ $order->sold_proof_rejected_note }}</p>
            </div>
        @endif

        @if($order->sold_proof_photo)
            <div class="mb-4 text-sm">
                <a href="{{ asset('storage/'.$order->sold_proof_photo) }}" target="_blank" rel="noopener" class="underline">Lihat foto bukti terakhir</a>
                <p class="mt-1" style="color:var(--color-muted)">{{ $order->sold_proof_notes }}</p>
            </div>
        @endif

        @if($order->sold_proof_status !== \App\Enums\ProofStatus::Approved)
            <form action="{{ route('driver.proof', $order) }}" method="POST" enctype="multipart/form-data" class="driver-form-grid">
                @csrf
                <div class="driver-field">
                    <label for="sold_proof_photo">Foto dokumentasi sold</label>
                    <input id="sold_proof_photo" type="file" name="sold_proof_photo" accept="image/*" class="input" required>
                    @error('sold_proof_photo') <p class="text-xs mt-1" style="color:var(--color-danger)">{{ $message }}</p> @enderror
                </div>
                <div class="driver-field full">
                    <label for="sold_proof_notes">Keterangan</label>
                    <textarea id="sold_proof_notes" name="sold_proof_notes" rows="3" class="input" placeholder="Contoh: unit sudah diserahkan ke customer, ada tanda terima" required>{{ old('sold_proof_notes') }}</textarea>
                    @error('sold_proof_notes') <p class="text-xs mt-1" style="color:var(--color-danger)">{{ $message }}</p> @enderror
                </div>
                <div class="driver-field full driver-form-foot">
                    <p class="driver-hint">Bukti dikirim ke admin untuk diverifikasi sebelum status pesanan selesai.</p>
                    <button class="driver-btn">Kirim bukti sold</button>
                </div>
            </form>
        @endif
    </section>
</x-driver-layout>
