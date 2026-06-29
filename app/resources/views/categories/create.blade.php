@extends('layouts.dashboard', ['title' => 'Tambah Kategori'])

@section('dashboard-content')
    <section class="page-header">
        <div>
            <div class="mono-eyebrow">MASTER AKTIVITAS</div>
            <h1 class="page-title">Tambah kategori</h1>
        </div>
    </section>

    <section class="feature-card">
        <form method="POST" action="{{ route('categories.store') }}" class="form-grid">
            @csrf
            <div class="field"><span>Nama kategori</span><input type="text" name="name" required></div>
            <div class="field"><span>Deskripsi</span><input type="text" name="description"></div>
            <button type="submit" class="button button-primary">Simpan kategori</button>
        </form>
    </section>
@endsection
