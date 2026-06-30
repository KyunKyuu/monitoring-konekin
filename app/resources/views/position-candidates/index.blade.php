@extends('layouts.dashboard', ['title' => 'Kandidat Jabatan'])

@section('dashboard-content')
    <section class="page-header">
        <div class="page-header-block">
            <div class="mono-eyebrow">PENGURUS IDEAL</div>
            <h1 class="page-title">Kandidat posisi dan statusnya</h1>
            <p class="page-copy">Lihat siapa yang sedang diproyeksikan ke jabatan tertentu dan bagaimana status penetapannya.</p>
            <div class="inline-metrics">
                <span class="metric-pill"><strong>{{ $candidates->total() }}</strong> kandidat</span>
            </div>
        </div>
        <a href="{{ route('position-candidates.create') }}" class="button button-primary">Tambah kandidat</a>
    </section>

    <section class="feature-card">
        <div class="priority-list">
            @forelse ($candidates as $candidate)
                <div class="priority-card">
                    <div class="priority-head">
                        <strong>{{ $candidate->member->name }} · {{ $candidate->idealPosition->position_name }}</strong>
                        <span>{{ $candidate->idealPosition->function_name }} · {{ $candidate->status }}</span>
                    </div>
                    <p>{{ $candidate->notes ?: 'Belum ada catatan kandidat.' }}</p>
                    <div class="row-actions">
                        <a href="{{ route('position-candidates.edit', $candidate) }}" class="table-action-link">Edit</a>
                        <form method="POST" action="{{ route('position-candidates.destroy', $candidate) }}" onsubmit="return confirm('Hapus kandidat ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="button-link danger-link">Hapus</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="empty-state">Belum ada kandidat jabatan.</p>
            @endforelse
        </div>

        <div class="pagination-wrap">{{ $candidates->links() }}</div>
    </section>
@endsection
