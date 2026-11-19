<x-layout title="Chat">

<div class="container-apple py-10" style="max-width:700px">
    <div class="flex items-end justify-between gap-4 mb-6">
        <div>
            <h1 class="section-title mb-0">Chat</h1>
            <p class="text-sm mt-2" style="color:var(--color-muted)">Percakapan baru muncul otomatis tanpa perlu refresh.</p>
        </div>
        <span class="inline-flex items-center gap-1.5 text-xs" style="color:var(--color-muted)">
            <span class="chat-online-dot"></span> Terhubung
        </span>
    </div>

    <div id="chat-thread-list" data-poll-url="{{ route('chat.poll-index') }}">
        @if($threads->isEmpty())
            <div class="chat-empty text-center py-24" style="color:var(--color-muted)">
                <p class="text-5xl mb-3">💬</p>
                <p class="font-semibold text-lg">Belum ada percakapan</p>
                <p class="mt-1 text-sm">Mulai chat dari halaman produk.</p>
                <a href="{{ route('listings.index') }}" class="btn btn-primary btn-sm mt-5">Jelajahi katalog</a>
            </div>
        @else
            <div class="space-y-2">
                @foreach($threads as $thread)
                    @php $other = $thread->counterpartFor($user); $last = $thread->latestMessage; @endphp
                    <div class="card flex items-center gap-3 p-4 hover:shadow-lg transition-shadow {{ $thread->buyer_id === $user->id && $thread->unread_count > 0 ? 'chat-thread-unread' : '' }}"
                         data-thread-id="{{ $thread->id }}"
                         data-last-id="{{ $last?->id ?? 0 }}"
                         data-unread="{{ $thread->buyer_id === $user->id ? $thread->unread_count : 0 }}">
                        <a href="{{ route('chat.show', $thread) }}" class="flex items-center gap-3 flex-1 min-w-0">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0 text-sm" style="background:{{ $other->avatar_color }}">
                                {{ $other->initials() }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="font-semibold text-sm truncate">{{ $other->name }}</p>
                                    @if($last)
                                        <span class="text-xs flex-shrink-0 chat-relative-time" style="color:var(--color-muted)"
                                              data-age="{{ (int) abs(now()->diffInSeconds($last->created_at)) }}">{{ $last->created_at->diffForHumans() }}</span>
                                    @endif
                                </div>
                                <p class="text-xs mt-0.5 truncate" style="color:var(--color-muted)">{{ $thread->listing?->model ?? 'Konsultasi penjualan' }} {{ $thread->listing?->storageLabel() }}</p>
                                @if($last)
                                    <p class="text-xs truncate mt-0.5" style="color:var(--color-muted)">{{ $last->body }}</p>
                                @endif
                            </div>
                            @if($thread->buyer_id === $user->id && $thread->unread_count > 0)
                                <span class="w-5 h-5 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0" style="background:var(--color-accent)">{{ $thread->unread_count }}</span>
                            @endif
                        </a>
                        <form action="{{ route('chat.destroy', $thread) }}" method="POST" class="chat-thread-delete-form flex-shrink-0"
                              onsubmit="return confirm('Hapus percakapan ini? Semua pesan akan terhapus permanen dan tidak bisa dibatalkan.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="chat-thread-delete-btn" title="Hapus percakapan" aria-label="Hapus percakapan">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6M10 11v5M14 11v5"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

</x-layout>
