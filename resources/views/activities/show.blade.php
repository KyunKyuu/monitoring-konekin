@extends('layouts.dashboard', ['title' => $activity->title])

@section('dashboard-content')
    <section class="page-header">
        <div>
            <div class="mono-eyebrow">DETAIL KEGIATAN</div>
            <h1 class="page-title">{{ $activity->title }}</h1>
            <p class="page-copy">{{ $activity->scheduled_at->format('d M Y H:i') }} · {{ $activity->location ?: 'Lokasi belum diisi' }}</p>
        </div>
        <div class="hero-actions">
            <a href="{{ route('activities.edit', $activity) }}" class="button button-secondary">Edit</a>
            @if (auth()->user()?->hasRole('super_admin'))
                <form method="POST" action="{{ route('activities.destroy', $activity) }}" onsubmit="return confirm('Hapus kegiatan ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="button button-ghost">Hapus</button>
                </form>
            @endif
        </div>
    </section>

    <section class="content-grid">
        <article class="feature-card">
            <div class="section-heading"><div><div class="mono-eyebrow">KONTEKS</div><h2>Ringkasan</h2></div></div>
            <div class="detail-list">
                <div><span>Kategori</span><strong>{{ $activity->categoryModel?->name ?: $activity->category }}</strong></div>
                <div><span>Sub kategori</span><strong>{{ $activity->subcategoryModel?->name ?: ($activity->sub_category ?: '-') }}</strong></div>
                <div><span>Status</span><strong>{{ $activity->status }}</strong></div>
                <div><span>Pembuat</span><strong>{{ $activity->creator->name }}</strong></div>
            </div>
            @if($activity->summary_note)
                <p class="page-copy">{{ $activity->summary_note }}</p>
            @endif
        </article>

        <article class="feature-card">
            <div class="section-heading"><div><div class="mono-eyebrow">PARTISIPAN</div><h2>Member terkait</h2></div></div>
            <div class="signal-list">
                @forelse ($activity->members as $member)
                    <div class="signal-row">
                        <span class="tag-chip">{{ $member->pivot->role_in_activity }}</span>
                        <p>{{ $member->name }} · {{ $member->pivot->attendance_status }}</p>
                    </div>
                @empty
                    <p class="empty-state">Belum ada partisipan.</p>
                @endforelse
            </div>
        </article>
    </section>

    <section class="feature-card">
        <div class="section-heading"><div><div class="mono-eyebrow">NOTE TERKAIT</div><h2>Catatan individu dari kegiatan</h2></div></div>
        <div class="priority-list">
            @forelse ($activity->notes as $note)
                <div class="priority-card">
                    <div class="priority-head">
                        <strong>{{ $note->member->name }} · {{ $note->tag }}</strong>
                        <span>{{ $note->author->name }}</span>
                    </div>
                    <p>{{ $note->content }}</p>
                </div>
            @empty
                <p class="empty-state">Belum ada note yang terhubung ke kegiatan ini.</p>
            @endforelse
        </div>
    </section>
@endsection
