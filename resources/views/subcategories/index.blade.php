@extends('layouts.dashboard', ['title' => 'Sub Kategori'])

@section('dashboard-content')
    <section class="page-header">
        <div class="page-header-block">
            <div class="mono-eyebrow">MASTER AKTIVITAS</div>
            <h1 class="page-title">Sub kategori kegiatan</h1>
            <p class="page-copy">Sub kategori membantu detail klasifikasi aktivitas agar evaluasi per jenis kegiatan lebih rapi.</p>
            <div class="inline-metrics">
                <span class="metric-pill"><strong>{{ $subcategories->count() }}</strong> sub kategori</span>
            </div>
        </div>
        <a href="{{ route('subcategories.create') }}" class="button button-primary">Tambah sub kategori</a>
    </section>

    <section class="feature-card">
        <div class="table-list">
            @forelse ($subcategories as $subcategory)
                <div class="table-row">
                    <div>
                        <strong>{{ $subcategory->name }}</strong>
                        <p>{{ $subcategory->description ?: '-' }}</p>
                    </div>
                    <div>{{ $subcategory->category->name }}</div>
                    <div>{{ $subcategory->is_active ? 'Aktif' : 'Nonaktif' }}</div>
                    <div class="row-actions">
                        <a href="{{ route('subcategories.edit', $subcategory) }}" class="table-action-link">Edit</a>
                        <form method="POST" action="{{ route('subcategories.destroy', $subcategory) }}" onsubmit="return confirm('Hapus sub kategori ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="button-link danger-link">Hapus</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="empty-state">Belum ada sub kategori.</p>
            @endforelse
        </div>
    </section>
@endsection
