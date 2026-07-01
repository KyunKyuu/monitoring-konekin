@extends('layouts.dashboard', ['title' => 'Dashboard Pengurus'])

@section('dashboard-content')
    @php
        $dashboardUser = auth()->user();
    @endphp

    <section class="notion-page-header">
        <div>
            <div class="mono-eyebrow">WORKSPACE</div>
            <h1 class="page-title">Dashboard operasional</h1>
            <p class="page-copy">Ringkasan kerja pengurus: agenda, anggota prioritas, monitoring, dan keuangan.</p>
        </div>
        <div class="hero-actions">
            @if ($dashboardUser?->hasAnyRole(['super_admin', 'mentor']))
                <a href="{{ route('activities.create') }}" class="button button-primary">Input kegiatan</a>
                <a href="{{ route('notes.create') }}" class="button button-secondary">Tambah note</a>
                <a href="{{ route('progress.create') }}" class="button button-secondary">Catat progress</a>
            @endif
            @if ($dashboardUser?->hasAnyRole(['super_admin', 'pengurus_keuangan']))
                <a href="{{ route('cash-transactions.create') }}" class="button button-secondary">Catat kas</a>
            @endif
        </div>
    </section>

    <section class="notion-kpi-grid">
        @foreach ($stats as $stat)
            <article class="notion-kpi">
                <span>{{ $stat['label'] }}</span>
                <strong>{{ $stat['value'] }}</strong>
                <p>{{ $stat['meta'] }}</p>
            </article>
        @endforeach
    </section>

    @if ($dashboardUser?->hasAnyRole(['super_admin', 'mentor']))
        <section class="notion-columns">
            <article class="notion-section">
                <div class="section-heading">
                    <div>
                        <div class="mono-eyebrow">HARI INI</div>
                        <h2>Agenda</h2>
                    </div>
                    <a href="{{ route('activities.calendar') }}" class="button button-ghost-sm">Kalender</a>
                </div>

                <div class="notion-list">
                    @forelse ($todayAgenda as $item)
                        <a href="{{ route('activities.show', $item) }}" class="notion-list-row">
                            <div>
                                <strong>{{ $item->title }}</strong>
                                <p>{{ $item->scheduled_at->format('H:i') }} - {{ $item->category }}{{ $item->sub_category ? ' / '.$item->sub_category : '' }}</p>
                            </div>
                            <span class="status-pill">{{ $item->status }}</span>
                        </a>
                    @empty
                        <p class="empty-state">Belum ada agenda hari ini.</p>
                    @endforelse
                </div>
            </article>

            <article class="notion-section">
                <div class="section-heading">
                    <div>
                        <div class="mono-eyebrow">PRIORITAS</div>
                        <h2>Member perlu perhatian</h2>
                    </div>
                    <a href="{{ route('members.index') }}" class="button button-ghost-sm">Member</a>
                </div>

                <div class="notion-list">
                    @forelse ($focusMembers as $member)
                        <a href="{{ route('members.show', $member) }}" class="notion-list-row">
                            <div>
                                <strong>{{ $member->name }}</strong>
                                <p>{{ $member->target_role ?: 'Belum ada target' }} - {{ $member->target_function ?: 'fungsi belum ditentukan' }}</p>
                            </div>
                            <span class="status-pill">{{ $member->note_priority }}</span>
                        </a>
                    @empty
                        <p class="empty-state">Belum ada member prioritas.</p>
                    @endforelse
                </div>
            </article>
        </section>

        <section class="notion-columns">
            <article class="notion-section">
                <div class="section-heading">
                    <div>
                        <div class="mono-eyebrow">MONITORING</div>
                        <h2>Sinyal evaluasi</h2>
                    </div>
                    <a href="{{ route('notes.index') }}" class="button button-ghost-sm">Note</a>
                </div>

                <div class="notion-list">
                    @forelse ($signals as $signal)
                        <div class="notion-list-row">
                            <div>
                                <strong>{{ $signal->tag }}</strong>
                                <p>{{ $signal->aggregate }} note memakai tag ini</p>
                            </div>
                        </div>
                    @empty
                        <p class="empty-state">Belum ada note bertag.</p>
                    @endforelse
                </div>
            </article>

            <article class="notion-section">
                <div class="section-heading">
                    <div>
                        <div class="mono-eyebrow">PROGRESS</div>
                        <h2>Update terbaru</h2>
                    </div>
                    <a href="{{ route('progress.index') }}" class="button button-ghost-sm">Progress</a>
                </div>

                <div class="notion-list">
                    @forelse ($recentProgress as $item)
                        <div class="notion-list-row">
                            <div>
                                <strong>{{ $item->member?->name ?: $item->area }}</strong>
                                <p>{{ $item->status }} - {{ $item->summary }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="empty-state">Belum ada update progress.</p>
                    @endforelse
                </div>
            </article>
        </section>
    @endif

    @if ($dashboardUser?->hasAnyRole(['super_admin', 'pengurus_keuangan']))
    <section class="notion-columns {{ $dashboardUser?->hasRole('super_admin') ? '' : 'notion-columns-single' }}">
        @if ($dashboardUser?->hasAnyRole(['super_admin', 'pengurus_keuangan']))
        <article class="notion-section">
            <div class="section-heading">
                <div>
                    <div class="mono-eyebrow">KEUANGAN</div>
                    <h2>Kas dan iuran</h2>
                </div>
                @if ($dashboardUser?->hasAnyRole(['super_admin', 'pengurus_keuangan']))
                    <a href="{{ route('contributions.index') }}" class="button button-ghost-sm">Keuangan</a>
                @endif
            </div>

            <div class="notion-table">
                <div class="notion-table-row finance-row">
                    <span>Iuran outstanding</span>
                    <strong>Rp {{ number_format($financeSnapshot['outstanding_amount'], 0, ',', '.') }}</strong>
                </div>
                <div class="notion-table-row finance-row">
                    <span>Pemasukan bulan ini</span>
                    <strong>Rp {{ number_format($financeSnapshot['month_income'], 0, ',', '.') }}</strong>
                </div>
                <div class="notion-table-row finance-row">
                    <span>Pengeluaran bulan ini</span>
                    <strong>Rp {{ number_format($financeSnapshot['month_expense'], 0, ',', '.') }}</strong>
                </div>
            </div>
        </article>
        @endif

        @if ($dashboardUser?->hasRole('super_admin'))
        <article class="notion-section">
            <div class="section-heading">
                <div>
                    <div class="mono-eyebrow">PENGURUS</div>
                    <h2>Akun login</h2>
                </div>
                @if (auth()->user()?->hasRole('super_admin'))
                    <a href="{{ route('operators.index') }}" class="button button-ghost-sm">Monitoring</a>
                @endif
            </div>

            <div class="notion-list">
                @foreach ($operatorStats as $operator)
                    <div class="notion-list-row">
                        <div>
                            <strong>{{ $operator['label'] }}</strong>
                            <p>{{ $operator['value'] }} akun</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </article>
        @endif
    </section>
    @endif
@endsection
