/**
 * hs·iphone — vanilla JS
 * Handles: mobile nav, toast, wishlist/cart AJAX, filter panel, photo preview, chat autoscroll
 */

// ── Helpers ──────────────────────────────────────────────────────────────────
const $ = (sel, ctx = document) => ctx.querySelector(sel);
const $$ = (sel, ctx = document) => [...ctx.querySelectorAll(sel)];

function toast(msg, type = 'default') {
    const container = $('#toast-container') ?? (() => {
        const el = document.createElement('div');
        el.id = 'toast-container';
        document.body.appendChild(el);
        return el;
    })();
    const el = document.createElement('div');
    el.className = `toast ${type === 'success' ? 'toast-success' : type === 'error' ? 'toast-error' : ''}`;
    el.textContent = msg;
    container.appendChild(el);
    setTimeout(() => el.remove(), 3500);
}

// ── Flash messages → toast ────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    $$('[data-flash]').forEach(el => {
        toast(el.dataset.flash, el.dataset.flashType ?? 'default');
        el.remove();
    });
});

// ── Mobile nav toggle ────────────────────────────────────────────────────────
// ▓▓ Editorial nav: scroll state, mobile menu, click feedback ▓▓▓▓▓▓▓▓▓▓▓▓
document.addEventListener('DOMContentLoaded', () => {
    const nav = $('#ed-nav');
    const btn = $('#nav-menu-btn');
    const menu = $('#nav-mobile-menu');
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const syncNav = () => {
        if (nav && nav.classList.contains('ed-nav-overlay')) {
            nav.classList.toggle('is-scrolled', window.scrollY > 40);
        }
    };
    syncNav();
    window.addEventListener('scroll', syncNav, { passive: true });

    if (btn && menu) {
        btn.addEventListener('click', () => {
            const open = menu.classList.toggle('is-open');
            btn.setAttribute('aria-expanded', String(open));
            menu.setAttribute('aria-hidden', String(!open));
        });
        $$('a', menu).forEach(link => link.addEventListener('click', () => {
            menu.classList.remove('is-open');
            btn.setAttribute('aria-expanded', 'false');
            menu.setAttribute('aria-hidden', 'true');
        }));
    }

    $$('.ed-nav .ed-click').forEach(el => {
        el.addEventListener('click', (e) => {
            if (reduceMotion) return;
            const href = el.getAttribute('href');
            if (!href || href.startsWith('#') || el.target === '_blank') return;
            e.preventDefault();
            el.classList.add('is-clicked');
            setTimeout(() => { window.location.href = href; }, 260);
        });
    });
});

// ── Wishlist toggle (AJAX) ────────────────────────────────────────────────────
document.addEventListener('click', async (e) => {
    const btn = e.target.closest('[data-wishlist-id]');
    if (!btn) return;
    e.preventDefault();
    const id = btn.dataset.wishlistId;
    try {
        const res = await fetch(`/wishlist/${id}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' },
        });
        if (!res.ok) { window.location = '/masuk'; return; }
        const { added, count } = await res.json();
        btn.classList.toggle('text-red-500', added);
        btn.classList.toggle('is-active', added);
        btn.setAttribute('aria-pressed', String(added));
        $$('[data-wishlist-count]').forEach(el => el.textContent = count);
        toast(added ? 'Ditambahkan ke wishlist ♥' : 'Dihapus dari wishlist', added ? 'success' : 'default');
    } catch { toast('Terjadi kesalahan', 'error'); }
});

// ── Cart add (AJAX) ───────────────────────────────────────────────────────────
document.addEventListener('click', async (e) => {
    const btn = e.target.closest('[data-cart-id]');
    if (!btn) return;
    e.preventDefault();
    const id = btn.dataset.cartId;
    try {
        const res = await fetch(`/keranjang/${id}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' },
        });
        if (!res.ok) { window.location = '/masuk'; return; }
        const { count } = await res.json();
        $$('[data-cart-count]').forEach(el => el.textContent = count);
        toast('Ditambahkan ke keranjang 🛍', 'success');
    } catch { toast('Terjadi kesalahan', 'error'); }
});

// ── Filter panel (mobile) ─────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    const openBtn = $('#filter-open-btn');
    const closeBtn = $('#filter-close-btn');
    const panel = $('#filter-panel');
    if (openBtn && panel) {
        openBtn.addEventListener('click', () => panel.classList.remove('hidden'));
        closeBtn?.addEventListener('click', () => panel.classList.add('hidden'));
    }
});

// ── Photo preview ─────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    const input = $('#photo-input');
    const preview = $('#photo-preview');
    if (!input || !preview) return;
    input.addEventListener('change', () => {
        preview.innerHTML = '';
        [...input.files].slice(0, 5).forEach(file => {
            const url = URL.createObjectURL(file);
            const img = document.createElement('img');
            img.src = url;
            img.className = 'w-20 h-20 object-cover rounded-xl border border-gray-200';
            preview.appendChild(img);
        });
    });
});

// ── Chat realtime-lite: polling, typing indicator, instant send ───────────────
const escapeHtml = (value = '') => String(value).replace(/[&<>"']/g, (char) => ({
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;',
}[char]));

const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';

const pageLoadedAt = Date.now();

const relativeFromAge = (age = 0) => {
    const total = Math.max(0, Number(age) + Math.floor((Date.now() - pageLoadedAt) / 1000));
    if (total < 60) return 'baru saja';
    if (total < 3600) return `${Math.floor(total / 60)} menit yang lalu`;
    if (total < 86400) return `${Math.floor(total / 3600)} jam yang lalu`;
    if (total < 604800) return `${Math.floor(total / 86400)} hari yang lalu`;
    return new Date(Date.now() - total * 1000).toLocaleDateString('id-ID', {
        day: 'numeric', month: 'short', year: 'numeric', timeZone: 'Asia/Jakarta',
    });
};

const refreshRelativeTimes = () => {
    $$('.chat-relative-time[data-age]').forEach((el) => {
        el.textContent = relativeFromAge(el.dataset.age);
    });
};

const chatMessageNode = (message) => {
    const row = document.createElement('div');
    row.className = `flex ${message.mine ? 'justify-end' : 'justify-start'}`;
    row.dataset.messageId = message.id;

    const bubble = document.createElement('div');
    bubble.className = `bubble ${message.mine ? 'bubble-mine' : 'bubble-other'}`;

    const body = document.createElement('p');
    body.textContent = message.body;

    const time = document.createElement('p');
    time.className = 'text-xs mt-1 opacity-60';
    time.textContent = message.time;

    bubble.append(body, time);
    row.appendChild(bubble);
    return row;
};

document.addEventListener('DOMContentLoaded', () => {
    // Thread view
    const msgs = $('#chat-messages');
    if (msgs) {
        const pollUrl = msgs.dataset.pollUrl;
        const typingUrl = msgs.dataset.typingUrl;
        const storeUrl = msgs.dataset.storeUrl;
        const form = $('#chat-form');
        const input = $('#chat-message-input');
        const sendBtn = $('#chat-send-btn');
        const typingEl = $('#chat-typing');
        let lastId = Math.max(0, ...$$('[data-message-id]', msgs).map((el) => Number(el.dataset.messageId) || 0));
        let clearedAt = String(msgs.dataset.clearedAt ?? 0);
        let typingSentAt = 0;

        const atBottom = () => msgs.scrollHeight - msgs.scrollTop - msgs.clientHeight < 120;
        const scrollToBottom = () => { msgs.scrollTop = msgs.scrollHeight; };

        const appendMessages = (messages = [], force = false) => {
            const stick = force || atBottom();
            messages.forEach((message) => {
                if (!message?.id || Number(message.id) <= lastId) return;
                $('.chat-empty', msgs)?.remove();
                msgs.appendChild(chatMessageNode(message));
                lastId = Math.max(lastId, Number(message.id));
            });
            if (stick) requestAnimationFrame(scrollToBottom);
        };

        const poll = async () => {
            if (document.hidden || !pollUrl) return;
            try {
                const res = await fetch(`${pollUrl}?after=${lastId}`, {
                    headers: { Accept: 'application/json' },
                    credentials: 'same-origin',
                });
                if (!res.ok) return;
                const data = await res.json();

                // Riwayat dihapus pihak lain — kosongkan tampilan tanpa refresh
                const nextClearedAt = String(data.cleared_at ?? 0);
                if (nextClearedAt !== clearedAt) {
                    clearedAt = nextClearedAt;
                    lastId = 0;
                    msgs.innerHTML = '<p class="chat-empty text-center text-sm py-10" style="color:var(--color-muted)">Belum ada pesan. Mulai percakapan! 👋</p>';
                    if (typingEl) typingEl.hidden = true;
                }

                appendMessages(data.messages);
                if (typingEl) typingEl.hidden = !data.typing;
            } catch { /* offline / tunnel delay — try again next tick */ }
        };

        requestAnimationFrame(scrollToBottom);
        setInterval(poll, 2500);
        poll();
        window.addEventListener('focus', poll);

        if (input && typingUrl) {
            input.addEventListener('input', () => {
                const now = Date.now();
                if (!input.value.trim() || now - typingSentAt < 1500) return;
                typingSentAt = now;
                fetch(typingUrl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken(),
                        Accept: 'application/json',
                    },
                    body: JSON.stringify({}),
                }).catch(() => {});
            });
        }

        if (form && storeUrl) {
            form.addEventListener('submit', async (event) => {
                event.preventDefault();
                const body = input.value.trim();
                if (!body) return;

                if (sendBtn) sendBtn.disabled = true;
                try {
                    const res = await fetch(storeUrl, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken(),
                            Accept: 'application/json',
                        },
                        body: JSON.stringify({ body }),
                    });
                    if (!res.ok) throw new Error('Gagal mengirim pesan.');
                    const data = await res.json();
                    appendMessages([data.message], true);
                    input.value = '';
                    if (typingEl) typingEl.hidden = true;
                    poll();
                } catch (error) {
                    toast(error.message || 'Gagal mengirim pesan.', 'error');
                } finally {
                    if (sendBtn) sendBtn.disabled = false;
                    input.focus();
                }
            });
        }
    }

    // Conversation list view
    const list = $('#chat-thread-list');
    if (list?.dataset.pollUrl) {
        const emptyState = `
            <div class="chat-empty text-center py-24" style="color:var(--color-muted)">
                <p class="text-5xl mb-3">💬</p>
                <p class="font-semibold text-lg">Belum ada percakapan</p>
                <p class="mt-1 text-sm">Mulai chat dari halaman produk.</p>
                <a href="/iphone" class="btn btn-primary btn-sm mt-5">Jelajahi katalog</a>
            </div>`;

        const threadNode = (thread) => {
            const last = thread.last;
            const listing = thread.listing
                ? `${thread.listing.model} ${thread.listing.storage_label}`.trim()
                : 'Konsultasi penjualan';
            const csrf = csrfToken();

            return `<div class="card flex items-center gap-3 p-4 hover:shadow-lg transition-shadow ${thread.unread ? 'chat-thread-unread' : ''}" data-thread-id="${thread.id}" data-last-id="${last?.id ?? 0}" data-unread="${thread.unread}">
                <a href="${escapeHtml(thread.url)}" class="flex items-center gap-3 flex-1 min-w-0">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0 text-sm" style="background:${escapeHtml(thread.other.color)}">${escapeHtml(thread.other.initials)}</div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <p class="font-semibold text-sm truncate">${escapeHtml(thread.other.name)}</p>
                            ${last ? `<span class="text-xs flex-shrink-0 chat-relative-time" style="color:var(--color-muted)" data-age="${last.age ?? 0}">${escapeHtml(relativeFromAge(last.age ?? 0))}</span>` : ''}
                        </div>
                        <p class="text-xs mt-0.5 truncate" style="color:var(--color-muted)">${escapeHtml(listing)}</p>
                        ${last ? `<p class="text-xs truncate mt-0.5" style="color:var(--color-muted)">${escapeHtml(last.body)}</p>` : ''}
                    </div>
                    ${thread.unread ? `<span class="w-5 h-5 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0" style="background:var(--color-accent)">${thread.unread}</span>` : ''}
                </a>
                <form action="${escapeHtml(thread.delete_url)}" method="POST" class="chat-thread-delete-form flex-shrink-0" onsubmit="return confirm('Hapus percakapan ini? Semua pesan akan terhapus permanen dan tidak bisa dibatalkan.')">
                    <input type="hidden" name="_token" value="${escapeHtml(csrf)}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="chat-thread-delete-btn" title="Hapus percakapan" aria-label="Hapus percakapan">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6M10 11v5M14 11v5"/>
                        </svg>
                    </button>
                </form>
            </div>`;
        };

        const signature = () => $$('[data-thread-id]', list)
            .map((el) => `${el.dataset.threadId}:${el.dataset.lastId}:${el.dataset.unread}`)
            .join('|');

        let currentSignature = signature();

        const pollList = async () => {
            if (document.hidden) return;
            try {
                const res = await fetch(list.dataset.pollUrl, {
                    headers: { Accept: 'application/json' },
                    credentials: 'same-origin',
                });
                if (!res.ok) return;
                const data = await res.json();
                const nextSignature = (data.threads ?? [])
                    .map((thread) => `${thread.id}:${thread.last?.id ?? 0}:${thread.unread}`)
                    .join('|');
                if (nextSignature === currentSignature) return;

                currentSignature = nextSignature;
                list.innerHTML = data.threads?.length
                    ? `<div class="space-y-2">${data.threads.map(threadNode).join('')}</div>`
                    : emptyState;
                refreshRelativeTimes();
            } catch { /* silently retry */ }
        };

        setInterval(refreshRelativeTimes, 30000);
        setInterval(pollList, 3000);
        window.addEventListener('focus', pollList);
    }
});
