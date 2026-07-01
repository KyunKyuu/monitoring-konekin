@extends('layouts.dashboard', ['title' => 'Edit Kategori'])

@section('dashboard-content')
    <section class="page-header">
        <div>
            <div class="mono-eyebrow">MASTER AKTIVITAS</div>
            <h1 class="page-title">Edit kategori</h1>
        </div>
    </section>

    <section class="feature-card">
        <form method="POST" action="{{ route('categories.update', $category) }}" class="form-grid">
            @csrf
            @method('PUT')
            <div class="field"><span>Nama kategori</span><input type="text" name="name" value="{{ old('name', $category->name) }}" required></div>
            <div class="field"><span>Deskripsi</span><input type="text" name="description" value="{{ old('description', $category->description) }}"></div>
            <button type="submit" class="button button-primary">Update kategori</button>
        </form>
    </section>
@endsection
