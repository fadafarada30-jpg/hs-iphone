<x-layout :title="'Chat – '.($thread->listing?->model ?? 'Konsultasi penjualan')">

<div class="container-apple py-6" style="max-width:680px">
    {{-- Header --}}
    @php $other = $thread->counterpartFor($user); @endphp
    <div class="flex items-center gap-3 mb-4">
        <a href="{{ route('chat.index') }}" class="p-2 rounded-full hover:bg-black/5" aria-label="Kembali ke daftar chat">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg>
        </a>
        <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0" style="background:{{ $other->avatar_color }}">{{ $other->initials() }}</div>
        <div>
            <p class="font-semibold text-sm">{{ $other->name }}</p>
            @if($thread->listing)
                <a href="{{ route('listings.show', $thread->listing) }}" class="text-xs" style="color:var(--color-accent)">{{ $thread->listing->model }} {{ $thread->listing->storageLabel() }}</a>
            @else
                <span class="text-xs" style="color:var(--color-accent)">Konsultasi penjualan</span>
            @endif
        </div>
        <span class="ml-auto inline-flex items-center gap-1.5 text-xs" style="color:var(--color-muted)">
            <span class="chat-online-dot"></span> <span class="chat-status-label">Pembaruan otomatis</span>
        </span>
        <form id="chat-clear-form" action="{{ route('chat.clear', $thread) }}" method="POST"
              onsubmit="return confirm('Hapus semua pesan dalam percakapan ini? Riwayat di pihak lawan bicara juga ikut terhapus dan tidak bisa dibatalkan.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-ghost btn-sm chat-clear-btn" title="Hapus riwayat chat" aria-label="Hapus riwayat chat">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6M10 11v5M14 11v5"/>
                </svg>
                <span class="chat-clear-label">Hapus chat</span>
            </button>
        </form>
    </div>

    {{-- Messages --}}
    <div id="chat-messages"
         class="card chat-messages p-4 space-y-3 mb-3"
         data-thread-id="{{ $thread->id }}"
         data-user-id="{{ $user->id }}"
         data-poll-url="{{ route('chat.poll', $thread) }}"
         data-typing-url="{{ route('chat.typing', $thread) }}"
         data-store-url="{{ route('chat.store', $thread) }}"
         data-cleared-at="{{ $thread->cleared_at?->timestamp ?? 0 }}">
        @foreach($thread->messages as $msg)
            <div class="flex {{ $msg->isMine($user) ? 'justify-end' : 'justify-start' }}" data-message-id="{{ $msg->id }}">
                <div class="bubble {{ $msg->isMine($user) ? 'bubble-mine' : 'bubble-other' }}">
                    <p>{{ $msg->body }}</p>
                    <p class="text-xs mt-1 opacity-60">{{ $msg->created_at->format('H:i') }}</p>
                </div>
            </div>
        @endforeach

        @if($thread->messages->isEmpty())
            <p class="chat-empty text-center text-sm py-10" style="color:var(--color-muted)">Belum ada pesan. Mulai percakapan! 👋</p>
        @endif
    </div>

    {{-- Typing indicator --}}
    <div id="chat-typing" class="chat-typing mb-3" hidden aria-live="polite">
        <span class="chat-typing-dots"><span></span><span></span><span></span></span>
        <span>{{ $other->name }} sedang mengetik…</span>
    </div>

    {{-- Send form --}}
    <form id="chat-form" action="{{ route('chat.store', $thread) }}" method="POST" class="flex gap-2">
        @csrf
        <input id="chat-message-input" type="text" name="body" class="input flex-1" placeholder="Tulis pesan…" required autocomplete="off" maxlength="1000">
        <button id="chat-send-btn" type="submit" class="btn btn-primary">Kirim</button>
    </form>
    <p class="text-xs mt-3" style="color:var(--color-muted)">Pesan baru dan indikator mengetik muncul otomatis tanpa perlu menyegarkan halaman.</p>
</div>

</x-layout>
