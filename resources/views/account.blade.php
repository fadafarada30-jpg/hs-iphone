<x-layout title="Akun saya">
    <div class="account-stage">
        <div class="ed-wrap">
            <div class="account-head">
                <div>
                    <p class="sell-kicker">Akun / Profil</p>
                    <h1 class="sell-title">Profil kamu.</h1>
                </div>
                @if($user->joined_at)
                    <p class="account-meta">Bergabung {{ $user->joined_at->format('d M Y') }}</p>
                @endif
            </div>

            <div class="account-layout">
                {{-- Identity --}}
                <aside class="account-card account-identity">
                    <div class="account-avatar">{{ $user->initials() }}</div>
                    <h2 class="account-name">{{ $user->name }}</h2>
                    <p class="account-email">{{ $user->email }}</p>

                    <div class="account-tags">
                        <span class="account-tag">{{ $user->role->label() }}</span>
                        @if($user->city)
                            <span class="account-tag">{{ $user->city }}</span>
                        @endif
                        @if($user->reviews_count > 0)
                            <span class="account-tag">&#9733; {{ number_format($user->rating, 1) }} &middot; {{ $user->reviews_count }} ulasan</span>
                        @endif
                    </div>

                    @if($user->bio)
                        <p class="account-bio">{{ $user->bio }}</p>
                    @endif

                    <div class="account-divider"></div>
                    <p class="account-label">Informasi akun</p>
                    <div class="account-info-row">
                        <span>Peran</span>
                        <span>{{ $user->role->label() }}</span>
                    </div>
                    <div class="account-info-row">
                        <span>Kota</span>
                        <span>{{ $user->city ?: 'Belum diisi' }}</span>
                    </div>
                    <div class="account-info-row">
                        <span>Bergabung</span>
                        <span>{{ $user->joined_at ? $user->joined_at->format('d M Y') : 'Belum tercatat' }}</span>
                    </div>

                    <div class="account-divider"></div>
                    <p class="account-label">Aksi cepat</p>
                    <div class="account-actions">
                        <a href="{{ route('sell.create') }}" class="account-action">
                            <span>Jual / tukar tambah</span><span>&rarr;</span>
                        </a>
                        <a href="{{ route('sell.index') }}" class="account-action">
                            <span>Pengajuan jual saya</span><span>&rarr;</span>
                        </a>
                        @if($user->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="account-action">
                                <span>Konsol admin</span><span>&rarr;</span>
                            </a>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('logout') }}" class="account-logout">
                        @csrf
                        <button type="submit">Keluar dari akun</button>
                    </form>
                </aside>

                {{-- Main --}}
                <div>
                    <div class="account-stats">
                        <div class="account-stat">
                            <p class="account-stat-num">{{ $stats['orders'] }}</p>
                            <p class="account-stat-label">Pesanan</p>
                        </div>
                        <div class="account-stat">
                            <p class="account-stat-num">{{ $stats['requests'] }}</p>
                            <p class="account-stat-label">Pengajuan jual</p>
                        </div>
                        <div class="account-stat">
                            <p class="account-stat-num">{{ $stats['reviews'] }}</p>
                            <p class="account-stat-label">Ulasan</p>
                        </div>
                    </div>

                    {{-- Recent orders --}}
                    <section class="account-panel">
                        <div class="account-panel-head">
                            <h2 class="account-panel-title">Pesanan terbaru</h2>
                            <a href="{{ route('orders.index') }}" class="account-link">Lihat semua</a>
                        </div>
                        @if($recentOrders->isEmpty())
                            <p class="account-empty">Belum ada pesanan. Unit yang kamu pesan akan tampil di sini.</p>
                        @else
                            <div class="account-list">
                                @foreach($recentOrders as $order)
                                    <a href="{{ route('orders.show', $order) }}" class="account-row">
                                        <span class="account-row-lead">{{ $order->code }}</span>
                                        <span class="account-row-meta">{{ $order->created_at->format('d M Y') }} &middot; {{ count($order->items) }} unit</span>
                                        <span class="account-row-value">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                                        <span class="account-chip">{{ $order->status->label() }}</span>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </section>

                    {{-- Listings --}}
                    @if($user->isAdmin())
                        <section class="account-panel">
                            <div class="account-panel-head">
                                <h2 class="account-panel-title">Listing saya</h2>
                                <a href="{{ route('admin.sell-requests') }}" class="account-link">Kelola</a>
                            </div>
                            @if($user->listings->isEmpty())
                                <p class="account-empty">Belum ada listing aktif dari akun ini.</p>
                            @else
                                <div class="account-list">
                                    @foreach($user->listings as $listing)
                                        <a href="{{ route('listings.show', $listing) }}" class="account-row">
                                            <span class="account-row-lead">{{ $listing->model }}</span>
                                            <span class="account-row-meta">{{ $listing->storageLabel() }} &middot; {{ $listing->condition->label() }}</span>
                                            <span class="account-row-value">Rp {{ number_format($listing->price, 0, ',', '.') }}</span>
                                            <span class="account-chip">{{ $listing->status->label() }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </section>
                    @endif

                    {{-- Reviews --}}
                    @if($reviews->isNotEmpty())
                        <section class="account-panel">
                            <div class="account-panel-head">
                                <h2 class="account-panel-title">Ulasan dari pembeli</h2>
                                <span class="account-panel-note">{{ $reviews->count() }} terbaru</span>
                            </div>
                            <div>
                                @foreach($reviews as $review)
                                    <article class="account-review">
                                        <div class="account-review-avatar" style="background:{{ $review->reviewer->avatar_color }}">
                                            {{ $review->reviewer->initials() }}
                                        </div>
                                        <div>
                                            <div class="account-review-head">
                                                <span class="account-review-name">{{ $review->reviewer->name }}</span>
                                                <span class="account-stars">{{ str_repeat('★', $review->rating) }}</span>
                                            </div>
                                            <p class="account-review-body">{{ $review->body }}</p>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </section>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layout>
