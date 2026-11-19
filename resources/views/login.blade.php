<x-auth-layout title="Masuk">
    <div class="auth-stage">
        <div class="auth-card">
            <aside class="auth-aside">
                <div>
                    <p class="auth-eyebrow">HS·iPhone / member access</p>
                    <h2>Rumah untuk<br>iPhone pilihan.</h2>
                    <p class="auth-aside-copy">Masuk untuk menyimpan unit incaran, mengikuti pesanan, dan bertransaksi dengan lebih tenang.</p>
                </div>
                <div class="auth-aside-foot">
                    <span>Kualitas terverifikasi</span>
                    <span>Garansi jelas</span>
                    <span>Depok, Jawa Barat</span>
                </div>
            </aside>

            <div class="auth-panel">
                <h1 class="auth-title">Selamat datang kembali.</h1>
                <p class="auth-sub">Masuk menggunakan email dan kata sandi Anda.</p>

                @if ($errors->any())
                    <p class="auth-error">{{ $errors->first() }}</p>
                @endif

                <form method="POST" action="{{ route('login.store') }}" class="auth-form">
                    @csrf

                    <div class="auth-field">
                        <label for="email">Alamat email</label>
                        <input id="email" name="email" type="email" autocomplete="email" placeholder="nama@email.com" value="{{ old('email') }}" required autofocus>
                    </div>

                    <div class="auth-field">
                        <div class="auth-row">
                            <label for="password">Kata sandi</label>
                            <a class="auth-link" href="{{ route('register') }}">Lupa kata sandi?</a>
                        </div>
                        <input id="password" name="password" type="password" autocomplete="current-password" placeholder="Masukkan kata sandi" required>
                    </div>

                    <label class="auth-check">
                        <input type="checkbox" name="remember" value="1">
                        Ingat saya di perangkat ini
                    </label>

                    <button type="submit" class="auth-submit">Masuk ke akun</button>
                </form>

                <div class="auth-divider">atau coba akun demo</div>
                <div class="auth-demo">
                    <button type="button" data-fill-email="budi@demo.com">Pembeli</button>
                    <button type="button" data-fill-email="admin@demo.com">Admin</button>
                    <button type="button" data-fill-email="driver@demo.com">Driver COD</button>
                </div>

                <p class="auth-switch">Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a></p>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.querySelectorAll('[data-fill-email]').forEach((button) => {
                button.addEventListener('click', () => {
                    document.getElementById('email').value = button.dataset.fillEmail;
                    document.getElementById('password').value = 'password';
                });
            });
        </script>
    @endpush
</x-layout>
