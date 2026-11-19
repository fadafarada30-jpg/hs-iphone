@php
    $listing = $listing ?? null;
    $isEdit = $listing !== null;
    $val = function ($key, $default = null) use ($isEdit, $listing) {
        if (! $isEdit) {
            return old($key, $default);
        }
        $value = $listing->{$key};
        if ($value instanceof \BackedEnum) {
            $value = $value->value;
        }
        return old($key, $value ?? $default);
    };
    $completeness = old('completeness', $isEdit ? ($listing->completeness ?? []) : []);
    $isFeatured = $errors->any()
        ? (bool) old('is_featured')
        : ($isEdit ? (bool) $listing->is_featured : false);
@endphp

@if($errors->any())
    <div style="border:1px solid #efc3be;background:#fff1f0;color:#b42318;padding:14px 16px;font-size:11px;margin-bottom:18px;">
        Mohon periksa kembali field yang ditandai.
    </div>
@endif

<form action="{{ $isEdit ? route('admin.products.update', $listing) : route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="admin-form">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="admin-form-head">
        <h2>Detail unit</h2>
        <span>{{ $isEdit ? 'Edit listing' : 'Internal entry' }}</span>
    </div>

    <div class="admin-form-body">
        <div class="admin-form-grid">
            <div class="admin-field">
                <label for="model">Model iPhone</label>
                <input type="text" id="model" name="model" value="{{ $val('model') }}" placeholder="iPhone 15 Pro" required>
                @error('model') <p class="admin-error">{{ $message }}</p> @enderror
            </div>
            <div class="admin-field">
                <label for="series">Seri</label>
                <input type="text" id="series" name="series" value="{{ $val('series') }}" placeholder="15 Pro">
                @error('series') <p class="admin-error">{{ $message }}</p> @enderror
            </div>
            <div class="admin-field">
                <label for="storage_gb">Penyimpanan (GB)</label>
                <input type="number" id="storage_gb" name="storage_gb" value="{{ $val('storage_gb', 128) }}" min="16" max="2048" required>
                @error('storage_gb') <p class="admin-error">{{ $message }}</p> @enderror
            </div>
            <div class="admin-field">
                <label for="color">Warna</label>
                <input type="text" id="color" name="color" value="{{ $val('color') }}" placeholder="Natural Titanium" required>
                @error('color') <p class="admin-error">{{ $message }}</p> @enderror
            </div>
            <div class="admin-field">
                <label for="condition">Kondisi</label>
                <select id="condition" name="condition" required>
                    @foreach($conditions as $condition)
                        <option value="{{ $condition->value }}" @selected($val('condition', $conditions[0]->value) === $condition->value)>{{ $condition->label() }}</option>
                    @endforeach
                </select>
                @error('condition') <p class="admin-error">{{ $message }}</p> @enderror
            </div>
            <div class="admin-field">
                <label for="battery_health">Kesehatan baterai (%)</label>
                <input type="number" id="battery_health" name="battery_health" value="{{ $val('battery_health', 100) }}" min="0" max="100" required>
                @error('battery_health') <p class="admin-error">{{ $message }}</p> @enderror
            </div>
            <div class="admin-field">
                <label for="price">Harga jual (Rp)</label>
                <input type="number" id="price" name="price" value="{{ $val('price') }}" placeholder="12500000" min="0" required>
                @error('price') <p class="admin-error">{{ $message }}</p> @enderror
            </div>
            <div class="admin-field">
                <label for="original_price">Harga awal / sebelum diskon (Rp)</label>
                <input type="number" id="original_price" name="original_price" value="{{ $val('original_price') }}" placeholder="Opsional" min="0">
                @error('original_price') <p class="admin-error">{{ $message }}</p> @enderror
            </div>
            <div class="admin-field">
                <label for="warranty">Garansi</label>
                <select id="warranty" name="warranty" required>
                    @foreach($warranties as $warranty)
                        <option value="{{ $warranty->value }}" @selected($val('warranty', $warranties[0]->value) === $warranty->value)>{{ $warranty->label() }}</option>
                    @endforeach
                </select>
                @error('warranty') <p class="admin-error">{{ $message }}</p> @enderror
            </div>
            <div class="admin-field">
                <label for="location">Lokasi unit</label>
                <input type="text" id="location" name="location" value="{{ $val('location', 'Depok') }}" required>
                @error('location') <p class="admin-error">{{ $message }}</p> @enderror
            </div>
            <div class="admin-field">
                <label for="status">Status katalog</label>
                <select id="status" name="status" required>
                    @foreach($statuses as $status)
                        <option value="{{ $status->value }}" @selected($val('status', 'active') === $status->value)>{{ $status->label() }}</option>
                    @endforeach
                </select>
                @error('status') <p class="admin-error">{{ $message }}</p> @enderror
            </div>
            <div class="admin-field">
                <label for="photos">Foto produk</label>
                <input type="file" id="photos" name="photos[]" accept="image/*" multiple>
                <p class="admin-hint">Maksimal 5 foto total. Tiap file maksimal 2 MB.</p>
                @error('photos') <p class="admin-error">{{ $message }}</p> @enderror
                @error('photos.*') <p class="admin-error">{{ $message }}</p> @enderror
            </div>

            @if($isEdit && $listing->photos->isNotEmpty())
                <div class="admin-field full">
                    <label>Foto saat ini</label>
                    <div class="admin-photo-manage">
                        @foreach($listing->photos as $photo)
                            <div class="admin-photo-item">
                                <img src="{{ $photo->url() }}" alt="Foto produk">
                                <label>
                                    <input type="checkbox" name="remove_photos[]" value="{{ $photo->id }}">
                                    <span>Hapus foto</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                    <p class="admin-hint">Centang foto yang ingin dihapus, lalu simpan perubahan.</p>
                    @error('remove_photos') <p class="admin-error">{{ $message }}</p> @enderror
                </div>
            @endif

            <div class="admin-field full">
                <label>Kelengkapan</label>
                <div class="admin-checks">
                    @foreach(['Box', 'Charger', 'Kabel', 'Headset', 'Garansi resmi'] as $item)
                        <label class="admin-check">
                            <input type="checkbox" name="completeness[]" value="{{ $item }}" @checked(is_array($completeness) && in_array($item, $completeness))>
                            <span>{{ $item }}</span>
                        </label>
                    @endforeach
                </div>
                @error('completeness') <p class="admin-error">{{ $message }}</p> @enderror
            </div>

            <div class="admin-field full">
                <label for="description">Deskripsi</label>
                <textarea id="description" name="description" placeholder="Catatan kondisi, riwayat penggunaan, atau informasi penting lainnya.">{{ $val('description') }}</textarea>
                @error('description') <p class="admin-error">{{ $message }}</p> @enderror
            </div>

            <div class="admin-field full">
                <label class="admin-check" style="display:inline-flex;">
                    <input type="checkbox" name="is_featured" value="1" @checked($isFeatured)>
                    <span>Tampilkan sebagai unit pilihan di beranda</span>
                </label>
            </div>
        </div>

        <div class="admin-form-foot">
            @if($isEdit)
                <p class="admin-hint" style="margin:0;">Slug tidak diubah agar tautan existing tetap berfungsi.</p>
                <button type="submit" class="admin-btn">Simpan perubahan</button>
            @else
                <p class="admin-hint" style="margin:0;">Slug dibuat otomatis dari model, kapasitas, dan warna.</p>
                <button type="submit" class="admin-btn">Simpan produk</button>
            @endif
        </div>
    </div>
</form>
