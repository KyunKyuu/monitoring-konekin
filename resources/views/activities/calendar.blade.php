@extends('layouts.dashboard', ['title' => 'Kalender Kegiatan'])

@section('dashboard-content')
    <section class="page-header">
        <div>
            <div class="mono-eyebrow">KALENDER KEGIATAN</div>
            <h1 class="page-title">Agenda bulan {{ $month->translatedFormat('F Y') }}</h1>
        </div>
        <div class="hero-actions">
            <a href="{{ route('activities.index') }}" class="button button-secondary">Mode list</a>
            <a href="{{ route('activities.create') }}" class="button button-primary">Buat kegiatan</a>
        </div>
    </section>

    <section class="feature-card">
        <div class="calendar-toolbar">
            <a href="{{ route('activities.calendar', ['month' => $previousMonth->month, 'year' => $previousMonth->year]) }}" class="button button-ghost-sm">Bulan sebelumnya</a>
            <div class="calendar-label">{{ $month->translatedFormat('F Y') }}</div>
            <a href="{{ route('activities.calendar', ['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}" class="button button-ghost-sm">Bulan berikutnya</a>
        </div>

        <div class="calendar-grid calendar-head">
            <div>Minggu</div>
            <div>Senin</div>
            <div>Selasa</div>
            <div>Rabu</div>
            <div>Kamis</div>
            <div>Jumat</div>
            <div>Sabtu</div>
        </div>

        <div class="calendar-weeks">
            @foreach ($weeks as $week)
                <div class="calendar-grid">
                    @foreach ($week as $day)
                        <article class="calendar-day {{ $day['isCurrentMonth'] ? '' : 'calendar-day-muted' }} {{ $day['isToday'] ? 'calendar-day-today' : '' }}">
                            <div class="calendar-day-head">
                                <span>{{ $day['date']->day }}</span>
                                @if ($day['activities']->isNotEmpty())
                                    <small>{{ $day['activities']->count() }} agenda</small>
                                @endif
                            </div>

                            <div class="calendar-events">
                                @forelse ($day['activities']->take(3) as $activity)
                                    <a href="{{ route('activities.show', $activity) }}" class="calendar-event">
                                        <strong>{{ $activity->scheduled_at->format('H:i') }}</strong>
                                        <span>{{ $activity->title }}</span>
                                        <small>{{ $activity->subcategoryModel?->name ?: ($activity->categoryModel?->name ?: $activity->category) }}</small>
                                    </a>
                                @empty
                                    <span class="calendar-empty">Tidak ada agenda</span>
                                @endforelse

                                @if ($day['activities']->count() > 3)
                                    <div class="calendar-more">+{{ $day['activities']->count() - 3 }} agenda lain</div>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            @endforeach
        </div>
    </section>
@endsection
