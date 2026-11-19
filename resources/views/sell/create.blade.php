<x-layout title="Ajukan iPhone untuk Dijual">
    <div class="sell-stage">
        <div class="ed-wrap">
            <div class="sell-layout">
                <aside class="sell-intro">
                    <p class="sell-kicker">Jual / Tukar Tambah</p>
                    <h1 class="sell-title">Jual iPhone kamu dengan tenang.</h1>
                    <p class="sell-lede">Kirim spesifikasi unit kamu. Tim HS Depok akan meninjau pengajuan, lalu menghubungimu lewat chat untuk detail harga dan penyerahan barang.</p>

                    <div class="sell-steps">
                        <div class="sell-step">
                            <span class="sell-step-num">01</span>
                            <div>
                                <h3>Kirim spesifikasi</h3>
                                <p>Isi detail unit dan unggah foto terbaik agar kami bisa menilai kondisinya.</p>
                            </div>
                        </div>
                        <div class="sell-step">
                            <span class="sell-step-num">02</span>
                            <div>
                                <h3>Tim kami tinjau</h3>
                                <p>Pengajuan diperiksa sesuai standar kualitas dan kelengkapan toko.</p>
                            </div>
                        </div>
                        <div class="sell-step">
                            <span class="sell-step-num">03</span>
                            <div>
                                <h3>Lanjut lewat chat</h3>
                                <p>Jika disetujui, admin langsung membuka chat untuk negosiasi harga yang transparan.</p>
                            </div>
                        </div>
                    </div>

                    <p class="sell-note">Tidak ada biaya pendaftaran. Pengajuan tidak otomatis menjadi listing toko sampai kamu dan admin sepakat.</p>
                </aside>

                <section class="sell-form">
                    <div class="sell-form-head">
                        <h2>Detail unit</h2>
                        <span>Formulir 01 / 01</span>
                    </div>

                    <form action="{{ route('sell.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="sell-grid">
                            <div class="sell-field full">
                                <label for="model">Model iPhone</label>
                                <input id="model" type="text" name="model" value="{{ old('model') }}" placeholder="Contoh: iPhone 13 Pro" required>
                                @error('model') <p class="sell-error">{{ $message }}</p> @enderror
                            </div>

                            <div class="sell-field">
                                <label for="series">Seri</label>
                                <input id="series" type="text" name="series" value="{{ old('series') }}" placeholder="13">
                                @error('series') <p class="sell-error">{{ $message }}</p> @enderror
                            </div>

                            <div class="sell-field">
                                <label for="storage_gb">Kapasitas (GB)</label>
                                <input id="storage_gb" type="number" name="storage_gb" value="{{ old('storage_gb') }}" placeholder="128" required>
                                @error('storage_gb') <p class="sell-error">{{ $message }}</p> @enderror
                            </div>

                            <div class="sell-field">
                                <label for="color">Warna</label>
                                <input id="color" type="text" name="color" value="{{ old('color') }}" placeholder="Graphite" required>
                                @error('color') <p class="sell-error">{{ $message }}</p> @enderror
                            </div>

                            <div class="sell-field">
                                <label for="condition">Kondisi</label>
                                <select id="condition" name="condition" required>
                                    @foreach($conditions as $condition)
                                        <option value="{{ $condition->value }}" @selected(old('condition') === $condition->value)>{{ $condition->label() }}</option>
                                    @endforeach
                                </select>
                                @error('condition') <p class="sell-error">{{ $message }}</p> @enderror
                            </div>

                            <div class="sell-field">
                                <label for="battery_health">Kesehatan baterai (%)</label>
                                <input id="battery_health" type="number" name="battery_health" value="{{ old('battery_health') }}" placeholder="88" min="50" max="100">
                                @error('battery_health') <p class="sell-error">{{ $message }}</p> @enderror
                            </div>

                            <div class="sell-field">
                                <label for="city">Kota</label>
                                <input id="city" type="text" name="city" value="{{ old('city', auth()->user()->city) }}" required>
                                @error('city') <p class="sell-error">{{ $message }}</p> @enderror
                            </div>

                            <div class="sell-field full">
                                <label for="price">Harga harapan (Rp)</label>
                                <input id="price" type="number" name="price" value="{{ old('price') }}" placeholder="5000000" required>
                                @error('price') <p class="sell-error">{{ $message }}</p> @enderror
                            </div>

                            <div class="sell-field full">
                                <label for="description">Deskripsi / kelengkapan</label>
                                <textarea id="description" name="description" rows="4" placeholder="Ceritakan kondisi unit, kelengkapan, dan riwayat penggunaan">{{ old('description') }}</textarea>
                                @error('description') <p class="sell-error">{{ $message }}</p> @enderror
                            </div>

                            <div class="sell-field full">
                                <label for="photos">Foto unit (maks. 3)</label>
                                <input id="photos" type="file" name="photos[]" accept="image/*" multiple>
                                <p class="sell-hint">Unggah foto bagian depan, belakang, dan detail baterai jika memungkinkan.</p>
                                @error('photos.*') <p class="sell-error">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="sell-divider"></div>

                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <a href="{{ route('sell.index') }}" class="sell-back">Lihat pengajuan saya</a>
                            <button type="submit" class="sell-submit">Kirim pengajuan jual</button>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </div>
</x-layout>
