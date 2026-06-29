@extends('layouts.dashboard', ['title' => 'Kegiatan'])

@section('dashboard-content')
    <section class="page-header">
        <div>
            <div class="mono-eyebrow">KEGIATAN</div>
            <h1 class="page-title">Agenda dan kelas komunitas</h1>
        </div>
        <div class="hero-actions">
            <a href="{{ route('activities.import-template') }}" class="button button-secondary">Download template</a>
            <a href="{{ route('activities.calendar') }}" class="button button-secondary">Lihat kalender</a>
            <a href="{{ route('activities.create') }}" class="button button-primary">Buat kegiatan</a>
        </div>
    </section>

    <section class="feature-card">
        <div class="section-stack">
            <form method="POST" action="{{ route('activities.import') }}" enctype="multipart/form-data" class="form-grid">
                @csrf
                <div class="field field-full">
                    <span>Import CSV kegiatan</span>
                    <input type="file" name="file" accept=".csv,.txt" required>
                    <small class="field-hint">Kolom wajib: `title`, `category_name`, `scheduled_at`, `status`. Peserta diisi pada `member_codes` dengan pemisah `|`.</small>
                </div>
                <div class="hero-actions">
                    <button type="submit" class="button button-primary">Import kegiatan</button>
                </div>
            </form>
        </div>

        <div class="table-list">
            @forelse ($activities as $activity)
                <a href="{{ route('activities.show', $activity) }}" class="table-row table-row-link">
                    <div>
                        <strong>{{ $activity->title }}</strong>
                        <p>{{ $activity->theme ?: ($activity->subcategoryModel?->name ?: $activity->categoryModel?->name ?: $activity->category) }}</p>
                    </div>
                    <div>{{ $activity->scheduled_at->format('d M Y H:i') }}</div>
                    <div>{{ $activity->members->count() }} member</div>
                    <div class="row-actions">
                        <span class="status-pill">{{ $activity->status }}</span>
                        <span class="table-action-link">Buka</span>
                    </div>
                </a>
            @empty
                <p class="empty-state">Belum ada kegiatan.</p>
            @endforelse
        </div>

        <div class="pagination-wrap">{{ $activities->links() }}</div>
    </section>
@endsection
