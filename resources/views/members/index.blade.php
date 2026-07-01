@extends('layouts.dashboard', ['title' => 'Data Member'])

@section('dashboard-content')
    <section class="page-header">
        <div class="page-header-block">
            <div class="mono-eyebrow">MASTER MEMBER</div>
            <h1 class="page-title">Data anggota komunitas</h1>
            <p class="page-copy">Pusat data anggota untuk mapping pembinaan, target peran, dan prioritas monitoring.</p>
            <div class="inline-metrics">
                <span class="metric-pill"><strong>{{ $members->total() }}</strong> total member</span>
                <span class="metric-pill"><strong>{{ $search ? 'aktif' : 'semua' }}</strong> mode pencarian</span>
            </div>
        </div>
        <div class="hero-actions">
            <a href="{{ route('members.import-template') }}" class="button button-secondary">Download template</a>
            <a href="{{ route('members.create') }}" class="button button-primary">Tambah member</a>
        </div>
    </section>

    <section class="feature-card">
        <div class="stack-tight">
            <form method="GET" class="toolbar">
                <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama, kode, atau target role">
                <button type="submit" class="button button-ghost-sm">Cari</button>
            </form>

            <form method="POST" action="{{ route('members.import') }}" enctype="multipart/form-data" class="form-grid">
                @csrf
                <div class="field field-full">
                    <span>Import CSV member</span>
                    <input type="file" name="file" accept=".csv,.txt" required>
                    <small class="field-hint">Kolom wajib: `code`, `name`. Kolom opsional: `kaka_tingkat_code`, `gender`, `status`, `target_role`, `target_function`, `note_priority`.</small>
                </div>
                <div class="hero-actions">
                    <button type="submit" class="button button-primary">Import member</button>
                </div>
            </form>
        </div>

        <hr class="section-divider">

        <div class="table-list">
            @forelse ($members as $member)
                <a href="{{ route('members.show', $member) }}" class="table-row table-row-link">
                    <div>
                        <strong>{{ $member->name }}</strong>
                        <p>{{ $member->code }}</p>
                    </div>
                    <div>{{ $member->target_role ?: 'Belum ada target' }}</div>
                    <div>{{ $member->target_function ?: '-' }}</div>
                    <div class="row-actions">
                        <span class="status-pill">{{ $member->note_priority }}</span>
                        <span class="table-action-link">Buka</span>
                    </div>
                </a>
            @empty
                <p class="empty-state">Belum ada member.</p>
            @endforelse
        </div>

        <div class="pagination-wrap">{{ $members->links() }}</div>
    </section>
@endsection
