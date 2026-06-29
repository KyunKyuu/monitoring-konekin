@extends('layouts.app', ['title' => 'Login Pengurus', 'bodyClass' => 'auth-page'])

@section('content')
    <div class="auth-shell">
        <section class="auth-hero">
            <div class="mono-eyebrow">COMMUNITY OPERATIONS</div>
            <h1>Dashboard pengurus untuk monitoring member, kegiatan, dan pembinaan.</h1>
            <p>
                Satu tempat untuk melihat progres anggota, agenda hari ini, note evaluasi, dan arah pembinaan calon pengurus.
            </p>

            <div class="hero-signal-grid">
                <article class="signal-card">
                    <span class="signal-label">Monitoring</span>
                    <strong>Progres per anggota</strong>
                    <p>Riwayat kegiatan, note bertag, target pembinaan, dan tindak lanjut.</p>
                </article>
                <article class="signal-card">
                    <span class="signal-label">Keuangan</span>
                    <strong>Iuran dan kas</strong>
                    <p>Status iuran bulanan dan pengeluaran operasional tetap terpantau.</p>
                </article>
                <article class="signal-card">
                    <span class="signal-label">Mapping</span>
                    <strong>Pengurus ideal</strong>
                    <p>Posisi kosong, calon aktif, dan langkah pembinaan berikutnya.</p>
                </article>
            </div>
        </section>

        <section class="auth-panel">
            <div class="auth-card">
                <div class="mono-eyebrow">PENGURUS LOGIN</div>
                <h2>Masuk ke workspace internal</h2>
                <p class="auth-copy">Hanya pengurus dengan akun aktif yang bisa masuk ke sistem.</p>

                @if ($errors->any())
                    <div class="alert alert-error">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.store') }}" class="auth-form">
                    @csrf

                    <label class="field">
                        <span>Email atau username</span>
                        <input
                            type="text"
                            name="login"
                            value="{{ old('login') }}"
                            placeholder="admin@komunitas.local atau admin"
                            required
                            autofocus
                        >
                    </label>

                    <label class="field">
                        <span>Password</span>
                        <input
                            type="password"
                            name="password"
                            placeholder="Masukkan password"
                            required
                        >
                    </label>

                    <label class="checkbox-row">
                        <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                        <span>Pertahankan sesi login di browser ini</span>
                    </label>

                    <button type="submit" class="button button-primary">Masuk ke dashboard</button>
                </form>

                <div class="login-help">
                    <div>
                        <span class="mono-eyebrow">DEFAULT ADMIN</span>
                        <p>`admin` / `password`</p>
                    </div>
                    <div>
                        <span class="mono-eyebrow">DEFAULT MENTOR</span>
                        <p>`mentor` / `password`</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
