<x-auth-layout title="Daftar">
    <div class="auth-stage">
        <div class="auth-card">
            <aside class="auth-aside">
                <div>
                    <p class="auth-eyebrow">HS·iPhone / new member</p>
                    <h2>Bergabung dengan<br>komunitas iPhone.</h2>
                    <p class="auth-aside-copy">Daftar untuk membeli unit terverifikasi, menjual perangkat lama, dan memantau transaksi Anda di satu tempat.</p>
                </div>
                <div class="auth-aside-foot">
                    <span>Unit terkurasi</span>
                    <span>Proses transparan</span>
                    <span>Depok, Jawa Barat</span>
                </div>
            </aside>

            <div class="auth-panel">
                <h1 class="auth-title">Buat akun baru.</h1>
                <p class="auth-sub">Isi data di bawah untuk mulai bertransaksi.</p>

                @if ($errors->any())
                    <p class="auth-error">{{ $errors->first() }}</p>
                @endif

                <form method="POST" action="{{ route('register.store') }}" class="auth-form">
                    @csrf

                    <div class="auth-field">
                        <label for="name">Nama lengkap</label>
                        <input id="name" name="name" type="text" autocomplete="name" placeholder="Nama Anda" value="{{ old('name') }}" required autofocus>
                    </div>

                    <div class="auth-field">
                        <label for="email">Alamat email</label>
                        <input id="email" name="email" type="email" autocomplete="email" placeholder="nama@email.com" value="{{ old('email') }}" required>
                    </div>

                    <div class="auth-field">
                        <label for="city">Kota (opsional)</label>
                        <input id="city" name="city" type="text" autocomplete="address-level2" placeholder="Depok" value="{{ old('city') }}">
                    </div>

                    <div class="auth-field">
                        <label for="password">Kata sandi</label>
                        <input id="password" name="password" type="password" autocomplete="new-password" placeholder="Minimal 8 karakter" required>
                    </div>

                    <div class="auth-field">
                        <label for="password_confirmation">Konfirmasi kata sandi</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" placeholder="Ulangi kata sandi" required>
                    </div>

                    <button type="submit" class="auth-submit">Buat akun</button>
                </form>

                <p class="auth-switch">Sudah punya akun? <a href="{{ route('login') }}">Masuk sekarang</a></p>
                <p class="auth-note">Dengan mendaftar, Anda menyetujui penggunaan data untuk keperluan transaksi di HS·iPhone.</p>
            </div>
        </div>
    </div>
</x-layout>
