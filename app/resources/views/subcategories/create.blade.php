@extends('layouts.dashboard', ['title' => 'Tambah Sub Kategori'])

@section('dashboard-content')
    <section class="page-header">
        <div>
            <div class="mono-eyebrow">MASTER AKTIVITAS</div>
            <h1 class="page-title">Tambah sub kategori</h1>
        </div>
    </section>

    <section class="feature-card">
        <form method="POST" action="{{ route('subcategories.store') }}" class="form-grid">
            @csrf
            <div class="field"><span>Kategori induk</span>
                <select name="category_id" required>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field"><span>Nama sub kategori</span><input type="text" name="name" required></div>
            <div class="field field-full"><span>Deskripsi</span><textarea name="description" rows="4"></textarea></div>
            <button type="submit" class="button button-primary">Simpan sub kategori</button>
        </form>
    </section>
@endsection
