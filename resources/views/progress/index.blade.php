@extends('layouts.dashboard', ['title' => 'Progress Pembinaan'])

@section('dashboard-content')
    <section class="page-header">
        <div class="page-header-block">
            <div class="mono-eyebrow">PROGRESS PEMBINAAN</div>
            <h1 class="page-title">Riwayat perkembangan member</h1>
            <p class="page-copy">Catatan perkembangan untuk melihat anggota sudah sampai tahap mana dan apa tindak lanjut berikutnya.</p>
            <div class="inline-metrics">
                <span class="metric-pill"><strong>{{ $updates->total() }}</strong> total progress</span>
            </div>
        </div>
        <a href="{{ route('progress.create') }}" class="button button-primary">Catat progress</a>
    </section>

    <section class="feature-card">
        <div class="priority-list">
            @forelse ($updates as $update)
                <div class="priority-card">
                    <div class="priority-head">
                        <strong>{{ $update->member->name }} · {{ $update->area }}</strong>
                        <span>{{ $update->stage ?: 'Tanpa tahap' }} · {{ $update->status }}</span>
                    </div>
                    <p>{{ $update->summary }}</p>
                    <div class="row-actions">
                        <a href="{{ route('progress.edit', $update) }}" class="table-action-link">Edit</a>
                        <form method="POST" action="{{ route('progress.destroy', $update) }}" onsubmit="return confirm('Hapus progress ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="button-link danger-link">Hapus</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="empty-state">Belum ada progress pembinaan.</p>
            @endforelse
        </div>

        <div class="pagination-wrap">{{ $updates->links() }}</div>
    </section>
@endsection
