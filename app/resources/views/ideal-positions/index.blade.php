@extends('layouts.dashboard', ['title' => 'Pengurus Ideal'])

@section('dashboard-content')
    <section class="page-header">
        <div>
            <div class="mono-eyebrow">PENGURUS IDEAL</div>
            <h1 class="page-title">Struktur jabatan ideal</h1>
        </div>
        <div class="hero-actions">
            <a href="{{ route('ideal-positions.create') }}" class="button button-primary">Tambah posisi</a>
            <a href="{{ route('position-candidates.index') }}" class="button button-secondary">Lihat kandidat</a>
        </div>
    </section>

    <section class="feature-card">
        <div class="table-list">
            @forelse ($positions as $position)
                <div class="table-row">
                    <div>
                        <strong>{{ $position->position_name }}</strong>
                        <p>{{ $position->function_name }}</p>
                    </div>
                    <div>{{ $position->required_count }} kebutuhan</div>
                    <div>{{ $position->candidates_count }} kandidat</div>
                    <span class="status-pill">{{ $position->status }}</span>
                    <div class="row-actions">
                        <a href="{{ route('ideal-positions.edit', $position) }}" class="table-action-link">Edit</a>
                        <form method="POST" action="{{ route('ideal-positions.destroy', $position) }}" onsubmit="return confirm('Hapus posisi ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="button-link danger-link">Hapus</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="empty-state">Belum ada posisi ideal.</p>
            @endforelse
        </div>
    </section>
@endsection
