@extends('layouts.dashboard', ['title' => 'Tambah Akun Kas'])

@section('dashboard-content')
    <section class="page-header">
        <div>
            <div class="mono-eyebrow">KEUANGAN</div>
            <h1 class="page-title">Tambah akun kas</h1>
        </div>
    </section>

    <section class="feature-card">
        <form method="POST" action="{{ route('cash-accounts.store') }}" class="form-grid">
            @csrf
            <div class="field"><span>Kode kas</span><input type="text" name="code" required></div>
            <div class="field"><span>Nama kas</span><input type="text" name="name" required></div>
            <div class="field field-full"><span>Deskripsi</span><textarea name="description" rows="4">{{ old('description') }}</textarea></div>
            <button type="submit" class="button button-primary">Simpan akun kas</button>
        </form>
    </section>
@endsection
