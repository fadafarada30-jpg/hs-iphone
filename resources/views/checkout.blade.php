<x-layout title="Checkout">

<div class="container-apple py-10" style="max-width:800px">
    <h1 class="section-title mb-8">Checkout</h1>

    <div class="grid md:grid-cols-5 gap-8">
        {{-- Form --}}
        <div class="md:col-span-3">
            <form action="{{ route('checkout.store') }}" method="POST" class="space-y-5">
                @csrf

                <h2 class="font-semibold mb-3">Data penerima</h2>
                <div>
                    <label class="block text-sm font-medium mb-1">Nama penerima</label>
                    <input type="text" name="recipient_name" value="{{ old('recipient_name', auth()->user()->name) }}" class="input" required>
                    @error('recipient_name') <p class="text-xs mt-1" style="color:var(--color-danger)">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Nomor telepon</label>
                    <input type="tel" name="recipient_phone" value="{{ old('recipient_phone') }}" class="input" placeholder="08xxxxxxxxxx" required>
                    @error('recipient_phone') <p class="text-xs mt-1" style="color:var(--color-danger)">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Kota tujuan</label>
                    <input type="text" name="shipping_city" value="{{ old('shipping_city', auth()->user()->city) }}" class="input" required>
                    @error('shipping_city') <p class="text-xs mt-1" style="color:var(--color-danger)">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Alamat lengkap</label>
                    <textarea name="shipping_address" rows="3" class="input" required>{{ old('shipping_address') }}</textarea>
                    @error('shipping_address') <p class="text-xs mt-1" style="color:var(--color-danger)">{{ $message }}</p> @enderror
                </div>

                <h2 class="font-semibold mt-6 mb-3">Pengiriman</h2>
                <div class="space-y-2">
                    @foreach($shippingMethods as $method)
                        <label class="cursor-pointer block">
                            <input type="radio" name="shipping_method" value="{{ $method->value }}" @checked(old('shipping_method')===$method->value||$loop->first) class="sr-only peer">
                            <div class="card p-4 peer-checked:ring-2 peer-checked:ring-[var(--color-accent)] transition-all">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-semibold text-sm">{{ $method->label() }}</p>
                                        <p class="text-xs mt-0.5" style="color:var(--color-muted)">{{ $method->description() }}</p>
                                    </div>
                                    <span class="font-bold text-sm">Rp {{ number_format($method->cost(),0,',','.') }}</span>
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>

                {{-- Jadwal COD / ambil di toko --}}
                <div id="booking-fields" class="card p-4 space-y-3">
                    <h2 class="font-semibold">Jadwal pengambilan</h2>
                    <p class="text-xs" style="color:var(--color-muted)">Isi jadwal ini untuk metode COD atau datang ke toko.</p>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium mb-1">Tanggal</label>
                            <input type="date" name="booking_date" value="{{ old('booking_date') }}" class="input" min="{{ now()->toDateString() }}">
                            @error('booking_date') <p class="text-xs mt-1" style="color:var(--color-danger)">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Jam</label>
                            <input type="time" name="booking_time" value="{{ old('booking_time', '14:00') }}" class="input">
                            @error('booking_time') <p class="text-xs mt-1" style="color:var(--color-danger)">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div id="cod-address-field">
                        <label class="block text-sm font-medium mb-1">Alamat COD lengkap</label>
                        <textarea name="cod_address" rows="2" class="input" placeholder="Alamat lengkap untuk driver COD">{{ old('cod_address') }}</textarea>
                        @error('cod_address') <p class="text-xs mt-1" style="color:var(--color-danger)">{{ $message }}</p> @enderror
                    </div>
                </div>

                <h2 class="font-semibold mt-6 mb-3">Pembayaran</h2>
                <div class="space-y-2">
                    @foreach($paymentMethods as $pm)
                        <label class="cursor-pointer block">
                            <input type="radio" name="payment_method" value="{{ $pm->value }}" @checked(old('payment_method')===$pm->value||$loop->first) class="sr-only peer">
                            <div class="card p-4 peer-checked:ring-2 peer-checked:ring-[var(--color-accent)] transition-all">
                                <p class="font-semibold text-sm">{{ $pm->label() }}</p>
                                <p class="text-xs mt-0.5" style="color:var(--color-muted)">{{ $pm->description() }}</p>
                            </div>
                        </label>
                    @endforeach
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Catatan (opsional)</label>
                    <textarea name="notes" rows="2" class="input" placeholder="Pesan khusus untuk penjual">{{ old('notes') }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-full mt-2">Buat pesanan</button>
            </form>
        </div>

        {{-- Summary --}}
        <div class="md:col-span-2">
            <div class="card p-5 sticky top-20">
                <h2 class="font-semibold mb-4">Pesanan kamu</h2>
                <div class="space-y-3 mb-4">
                    @foreach($listings as $l)
                        <div class="flex gap-3 items-start">
                            <div class="w-12 h-12 rounded-xl overflow-hidden flex-shrink-0">
                                <x-catalog-photo alt="{{ $l->model }}" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium">{{ $l->model }}</p>
                                <p class="text-xs" style="color:var(--color-muted)">{{ $l->storageLabel() }} · {{ $l->color }}</p>
                                <p class="text-sm font-bold mt-0.5">Rp {{ number_format($l->price,0,',','.') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="pt-3 text-sm space-y-1" style="border-top:1px solid var(--color-border)">
                    <div class="flex justify-between">
                        <span style="color:var(--color-muted)">Subtotal</span>
                        <span>Rp {{ number_format($listings->sum('price'),0,',','.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span style="color:var(--color-muted)">Ongkir</span>
                        <span>Lihat pilihan</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const bookingFields = document.getElementById('booking-fields');
        const addressField = document.getElementById('cod-address-field');
        const addressInput = document.querySelector('[name="shipping_address"]');
        const codInput = document.querySelector('[name="cod_address"]');

        function syncBookingFields() {
            const selected = document.querySelector('[name="shipping_method"]:checked');
            const method = selected ? selected.value : '';
            const scheduled = method === 'cod' || method === 'pickup';

            if (bookingFields) bookingFields.style.display = scheduled ? '' : 'none';
            if (addressField) addressField.style.display = method === 'cod' ? '' : 'none';

            if (method === 'cod' && codInput && addressInput && !codInput.value.trim()) {
                codInput.value = addressInput.value.trim();
            }

            if (scheduled) {
                const codPayment = document.querySelector('[name="payment_method"][value="cod"]');
                if (codPayment) codPayment.checked = true;
            }
        }

        document.querySelectorAll('[name="shipping_method"]').forEach(function (radio) {
            radio.addEventListener('change', syncBookingFields);
        });
        syncBookingFields();
    });
</script>

</x-layout>
