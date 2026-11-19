@props(['conditions', 'warranties', 'series', 'filters'])

<form method="GET" action="{{ route('listings.index') }}" class="space-y-5">
    {{-- Search --}}
    <div>
        <label class="text-xs font-semibold uppercase tracking-wide mb-1 block" style="color:var(--color-muted)">Cari</label>
        <input type="text" name="cari" value="{{ $filters['cari'] ?? '' }}" class="input text-sm" placeholder="Model, warna…">
    </div>

    {{-- Series --}}
    <div>
        <p class="text-xs font-semibold uppercase tracking-wide mb-2" style="color:var(--color-muted)">Seri iPhone</p>
        <div class="flex flex-col gap-1.5">
            @foreach($series as $s)
                <label class="flex items-center gap-2 text-sm cursor-pointer">
                    <input type="radio" name="seri" value="{{ $s }}" @checked(($filters['seri']??'')===$s) class="accent-[var(--color-accent)]">
                    iPhone {{ $s }}
                </label>
            @endforeach
        </div>
    </div>

    {{-- Condition --}}
    <div>
        <p class="text-xs font-semibold uppercase tracking-wide mb-2" style="color:var(--color-muted)">Kondisi</p>
        <div class="flex flex-col gap-1.5">
            @foreach($conditions as $c)
                <label class="flex items-center gap-2 text-sm cursor-pointer">
                    <input type="radio" name="kondisi" value="{{ $c->value }}" @checked(($filters['kondisi']??'')===$c->value) class="accent-[var(--color-accent)]">
                    {{ $c->label() }}
                </label>
            @endforeach
        </div>
    </div>

    {{-- Price range --}}
    <div>
        <p class="text-xs font-semibold uppercase tracking-wide mb-2" style="color:var(--color-muted)">Harga (Rp)</p>
        <div class="flex gap-2">
            <input type="number" name="min_harga" value="{{ $filters['min_harga'] ?? '' }}" class="input text-sm" placeholder="Min" min="0" step="100000">
            <input type="number" name="max_harga" value="{{ $filters['max_harga'] ?? '' }}" class="input text-sm" placeholder="Maks" min="0" step="100000">
        </div>
    </div>

    {{-- Warranty --}}
    <div>
        <p class="text-xs font-semibold uppercase tracking-wide mb-2" style="color:var(--color-muted)">Garansi</p>
        <div class="flex flex-col gap-1.5">
            @foreach($warranties as $w)
                <label class="flex items-center gap-2 text-sm cursor-pointer">
                    <input type="radio" name="garansi" value="{{ $w->value }}" @checked(($filters['garansi']??'')===$w->value) class="accent-[var(--color-accent)]">
                    {{ $w->label() }}
                </label>
            @endforeach
        </div>
    </div>

    <div class="flex gap-2 pt-1">
        <button type="submit" class="btn btn-primary btn-sm flex-1">Terapkan</button>
        <a href="{{ route('listings.index') }}" class="btn btn-ghost btn-sm">Reset</a>
    </div>
</form>
