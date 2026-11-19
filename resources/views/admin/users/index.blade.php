<x-admin-layout title="Kelola pengguna">
    <div class="admin-page-head">
        <div>
            <p class="admin-kicker">Akses / Pengguna</p>
            <h1 class="admin-title">Pengguna.</h1>
            <p class="admin-subtitle">Lihat akun pembeli, admin, dan driver beserta aktivitas katalog serta pesanannya.</p>
        </div>
        <div class="admin-actions">
            <a href="{{ route('admin.dashboard') }}" class="admin-btn admin-btn--ghost admin-btn--sm">Dashboard</a>
        </div>
    </div>

    <div class="admin-stats">
        <div class="admin-stat">
            <p class="admin-stat-num">{{ number_format($counts['total']) }}</p>
            <p class="admin-stat-label">Total pengguna</p>
            <p class="admin-stat-foot">Seluruh akun terdaftar</p>
        </div>
        <div class="admin-stat">
            <p class="admin-stat-num">{{ number_format($counts['buyer']) }}</p>
            <p class="admin-stat-label">Pembeli</p>
            <p class="admin-stat-foot">Akun pelanggan toko</p>
        </div>
        <div class="admin-stat">
            <p class="admin-stat-num">{{ number_format($counts['admin']) }}</p>
            <p class="admin-stat-label">Admin</p>
            <p class="admin-stat-foot">Akses konsol internal</p>
        </div>
        <div class="admin-stat">
            <p class="admin-stat-num">{{ number_format($counts['driver']) }}</p>
            <p class="admin-stat-label">Driver COD</p>
            <p class="admin-stat-foot">Penanggung jadwal COD</p>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.users') }}" class="admin-filter">
        <input type="text" name="cari" value="{{ request('cari') }}" class="input" placeholder="Cari nama atau email.">
        <select name="role" class="input">
            <option value="">Semua role</option>
            <option value="buyer" @selected(request('role') === 'buyer')>Pembeli</option>
            <option value="admin" @selected(request('role') === 'admin')>Admin</option>
            <option value="driver" @selected(request('role') === 'driver')>Driver COD</option>
        </select>
        <button type="submit" class="admin-btn admin-btn--ghost admin-btn--sm">Filter</button>
        <span class="admin-filter-spacer"></span>
        <span class="admin-filter-count">{{ $users->total() }} pengguna ditemukan</span>
    </form>

    @if($users->isEmpty())
        <div class="admin-empty">Belum ada pengguna yang cocok dengan filter ini.</div>
    @else
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Pengguna</th>
                        <th>Email</th>
                        <th class="center">Role</th>
                        <th class="num">Listing</th>
                        <th class="num">Pesanan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $u)
                        <tr>
                            <td>
                                <div class="admin-user-cell">
                                    <span class="admin-avatar-sm" style="background:{{ $u->avatar_color }}">{{ $u->initials() }}</span>
                                    <div>
                                        <div class="admin-cell-title">{{ $u->name }}</div>
                                        <div class="admin-cell-meta">{{ $u->city ?: 'Kota belum diisi' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="admin-cell-meta">{{ $u->email }}</td>
                            <td class="center">
                                <span class="admin-pill {{ $u->role->value === 'admin' ? 'is-strong' : '' }} {{ $u->role->value === 'driver' ? 'is-quiet' : '' }}">
                                    {{ $u->role->label() }}
                                </span>
                            </td>
                            <td class="num">{{ $u->listings_count }}</td>
                            <td class="num">{{ $u->orders_count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="admin-filter" style="margin-top:18px; margin-bottom:0;">
            <span class="admin-filter-spacer"></span>
            {{ $users->links() }}
        </div>
    @endif
</x-admin-layout>
