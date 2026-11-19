<x-layout title="Catalog iPhone">

<div class="container-apple py-8">
    <div class="flex flex-col md:flex-row gap-8">

        {{-- Sidebar filters (desktop) --}}
        <aside class="hidden md:block w-56 flex-shrink-0">
            <x-filter-panel :conditions="$conditions" :warranties="$warranties" :series="$series" :filters="$filters" />
        </aside>

        {{-- Mobile filter overlay --}}
        <div id="filter-panel" class="fixed inset-0 z-50 hidden md:hidden" style="background:rgba(0,0,0,.4)">
            <div class="absolute right-0 top-0 h-full w-80 bg-white p-6 overflow-y-auto">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold">Filter</h3>
                    <button id="filter-close-btn" class="p-1 rounded-full hover:bg-gray-100">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>
                <x-filter-panel :conditions="$conditions" :warranties="$warranties" :series="$series" :filters="$filters" />
            </div>
        </div>

        {{-- Main content --}}
        <div class="flex-1 min-w-0">
            {{-- Top bar --}}
            <div class="flex flex-wrap items-center gap-3 mb-6">
                <div class="flex-1">
                    <h1 class="text-xl font-bold">iPhone Bekas</h1>
                    <p class="text-sm" style="color:var(--color-muted)">{{ $listings->total() }} listing ditemukan</p>
                </div>
                <button id="filter-open-btn" class="btn btn-ghost btn-sm md:hidden">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                    Filter
                </button>
                <form method="GET" action="{{ route('listings.index') }}" class="flex gap-2">
                    @foreach($filters as $k => $v)
                        @if($k !== 'urut' && $v !== '') <input type="hidden" name="{{ $k }}" value="{{ $v }}"> @endif
                    @endforeach
                    <select name="urut" onchange="this.form.submit()" class="input text-sm py-1.5" style="width:auto;padding-right:2rem">
                        <option value="terbaru" @selected(($filters['urut']??'terbaru')==='terbaru')>Terbaru</option>
                        <option value="harga_asc" @selected(($filters['urut']??'')==='harga_asc')>Harga: terendah</option>
                        <option value="harga_desc" @selected(($filters['urut']??'')==='harga_desc')>Harga: tertinggi</option>
                    </select>
                </form>
            </div>

            @if($listings->isEmpty())
                <div class="text-center py-24" style="color:var(--color-muted)">
                    <p class="text-4xl mb-3">📱</p>
                    <p class="font-semibold text-lg">Tidak ada listing ditemukan</p>
                    <p class="mt-1">Coba ubah filter atau kata kunci pencarian.</p>
                    <a href="{{ route('listings.index') }}" class="btn btn-ghost btn-sm mt-4">Hapus filter</a>
                </div>
            @else
                <div class="listings-grid">
                    @foreach($listings as $listing)
                        <x-listing-card :listing="$listing" />
                    @endforeach
                </div>
                <div class="mt-8">{{ $listings->links() }}</div>
            @endif
        </div>
    </div>
</div>

</x-layout>
