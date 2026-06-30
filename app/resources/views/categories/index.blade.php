@extends('layouts.dashboard', ['title' => 'Kategori'])

@section('dashboard-content')
    <section class="page-header">
        <div class="page-header-block">
            <div class="mono-eyebrow">MASTER AKTIVITAS</div>
            <h1 class="page-title">Kategori kegiatan</h1>
            <p class="page-copy">Master kategori dipakai untuk menjaga struktur kegiatan tetap konsisten dan bisa dipantau lintas agenda.</p>
            <div class="inline-metrics">
                <span class="metric-pill"><strong>{{ $categories->count() }}</strong> kategori</span>
            </div>
        </div>
        <div class="hero-actions">
            <a href="{{ route('subcategories.index') }}" class="button button-secondary">Sub kategori</a>
            <a href="{{ route('categories.create') }}" class="button button-primary">Tambah kategori</a>
        </div>
    </section>

    <section class="feature-card">
        <div class="table-list">
            @forelse ($categories as $category)
                <div class="table-row">
                    <div>
                        <strong>{{ $category->name }}</strong>
                        <p>{{ $category->description ?: '-' }}</p>
                    </div>
                    <div>{{ $category->subcategories_count }} sub kategori</div>
                    <div>{{ $category->is_active ? 'Aktif' : 'Nonaktif' }}</div>
                    <div class="row-actions">
                        <a href="{{ route('categories.edit', $category) }}" class="table-action-link">Edit</a>
                        <form method="POST" action="{{ route('categories.destroy', $category) }}" onsubmit="return confirm('Hapus kategori ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="button-link danger-link">Hapus</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="empty-state">Belum ada kategori.</p>
            @endforelse
        </div>
    </section>
@endsection
