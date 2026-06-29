@extends('layouts.dashboard', ['title' => 'Dashboard Pengurus', 'bodyClass' => 'dashboard-page'])

@section('dashboard-content')
    <section class="hero-panel">
        <div class="hero-copy">
            <div class="mono-eyebrow">OPERATIONS OVERVIEW</div>
            <h1>Dashboard pengurus untuk membaca kondisi anggota, agenda, dan alokasi tindak lanjut.</h1>
            <p>
                Fokus utamanya bukan hanya data masuk, tapi keputusan: siapa yang perlu dibina, fungsi mana yang masih kosong,
                dan agenda mana yang paling relevan untuk mapping anggota.
            </p>
            <div class="hero-actions">
                <a href="{{ route('activities.create') }}" class="button button-primary">Input kegiatan</a>
                <a href="{{ route('progress.create') }}" class="button button-secondary">Catat progress</a>
            </div>
        </div>
        <div class="hero-gradient-card">
            <div class="mono-eyebrow">TODAY SIGNAL</div>
            <strong>{{ $todayAgenda->count() }} agenda hari ini, {{ $financeSnapshot['outstanding_count'] }} iuran belum lunas.</strong>
            <p>Saldo aktif Rp {{ number_format($financeSnapshot['month_income'] - $financeSnapshot['month_expense'], 0, ',', '.') }} untuk arus bulan ini.</p>
        </div>
    </section>

    <section class="stats-grid">
        @foreach ($stats as $stat)
            <article class="stat-card">
                <span>{{ $stat['label'] }}</span>
                <strong>{{ $stat['value'] }}</strong>
                <p>{{ $stat['meta'] }}</p>
            </article>
        @endforeach
    </section>

    <section class="content-grid">
        <article class="feature-card">
            <div class="section-heading">
                <div>
                    <div class="mono-eyebrow">AGENDA HARI INI</div>
                    <h2>Kalender operasional</h2>
                </div>
                <a href="{{ route('activities.calendar') }}" class="button button-ghost-sm">Lihat kalender</a>
            </div>

            <div class="agenda-list">
                @foreach ($todayAgenda as $item)
                    <div class="agenda-row">
                        <div class="agenda-time">{{ $item->scheduled_at->format('H:i') }}</div>
                        <div class="agenda-body">
                            <strong>{{ $item->title }}</strong>
                            <p>{{ $item->category }}{{ $item->sub_category ? ' · '.$item->sub_category : '' }}</p>
                            <small>{{ $item->members->count() }} anggota / petugas terkait</small>
                        </div>
                        <span class="status-pill">{{ $item->status }}</span>
                    </div>
                @endforeach
                @if ($todayAgenda->isEmpty())
                    <p class="empty-state">Belum ada agenda terjadwal hari ini.</p>
                @endif
            </div>
        </article>

        <article class="feature-card">
            <div class="section-heading">
                <div>
                    <div class="mono-eyebrow">PEMBINAAN PRIORITAS</div>
                    <h2>Member yang perlu diarahkan</h2>
                </div>
                <a href="{{ route('members.index') }}" class="button button-ghost-sm">Buka member</a>
            </div>

            <div class="priority-list">
                @foreach ($focusMembers as $member)
                    <div class="priority-card">
                        <div class="priority-head">
                            <strong>{{ $member->name }}</strong>
                            <span>{{ $member->target_role ?: 'Belum ada target' }}</span>
                        </div>
                        <p>{{ $member->target_function ?: 'Fungsi belum ditentukan' }} · Prioritas {{ $member->note_priority }}</p>
                    </div>
                @endforeach
                @if ($focusMembers->isEmpty())
                    <p class="empty-state">Belum ada member prioritas.</p>
                @endif
            </div>
        </article>
    </section>

    <section class="content-grid">
        <article class="feature-card">
            <div class="section-heading">
                <div>
                    <div class="mono-eyebrow">VISUAL STATUS</div>
                    <h2>Distribusi status member</h2>
                </div>
            </div>

            <div class="chart-stack">
                @forelse ($chartData['memberStatus'] as $item)
                    <div class="chart-row">
                        <div class="chart-row-head">
                            <strong>{{ $item['label'] }}</strong>
                            <span>{{ $item['value'] }} member · {{ $item['percentage'] }}%</span>
                        </div>
                        <div class="chart-bar-track">
                            <div class="chart-bar-fill" style="width: {{ $item['percentage'] }}%; background: {{ $item['color'] }};"></div>
                        </div>
                    </div>
                @empty
                    <p class="empty-state">Belum ada data visual status member.</p>
                @endforelse
            </div>
        </article>

        <article class="feature-card">
            <div class="section-heading">
                <div>
                    <div class="mono-eyebrow">VISUAL AGENDA</div>
                    <h2>Distribusi status kegiatan</h2>
                </div>
            </div>

            <div class="chart-stack">
                @forelse ($chartData['activityStatus'] as $item)
                    <div class="chart-row">
                        <div class="chart-row-head">
                            <strong>{{ $item['label'] }}</strong>
                            <span>{{ $item['value'] }} kegiatan · {{ $item['percentage'] }}%</span>
                        </div>
                        <div class="chart-bar-track">
                            <div class="chart-bar-fill" style="width: {{ $item['percentage'] }}%; background: {{ $item['color'] }};"></div>
                        </div>
                    </div>
                @empty
                    <p class="empty-state">Belum ada data visual kegiatan.</p>
                @endforelse
            </div>
        </article>
    </section>

    <section class="content-grid">
        <article class="feature-card">
            <div class="section-heading">
                <div>
                    <div class="mono-eyebrow">MAPPING FUNGSI</div>
                    <h2>Arah target pembinaan</h2>
                </div>
                <a href="{{ route('targets.index') }}" class="button button-ghost-sm">Lihat target</a>
            </div>

            <div class="signal-list">
                @forelse ($functionMap as $function)
                    <div class="signal-row">
                        <span class="tag-chip">{{ $function->function_name }}</span>
                        <p>{{ $function->aggregate }} target pembinaan aktif / historis.</p>
                    </div>
                @empty
                    <p class="empty-state">Belum ada mapping fungsi pembinaan.</p>
                @endforelse
            </div>
        </article>

        <article class="feature-card">
            <div class="section-heading">
                <div>
                    <div class="mono-eyebrow">VISUAL FUNGSI</div>
                    <h2>Bobot target per fungsi</h2>
                </div>
                <a href="{{ route('targets.index') }}" class="button button-ghost-sm">Buka target</a>
            </div>

            <div class="chart-stack">
                @forelse ($chartData['functionMap'] as $item)
                    <div class="chart-row">
                        <div class="chart-row-head">
                            <strong>{{ $item['label'] }}</strong>
                            <span>{{ $item['value'] }} target · {{ $item['percentage'] }}%</span>
                        </div>
                        <div class="chart-bar-track">
                            <div class="chart-bar-fill" style="width: {{ $item['percentage'] }}%; background: {{ $item['color'] }};"></div>
                        </div>
                    </div>
                @empty
                    <p class="empty-state">Belum ada data visual fungsi.</p>
                @endforelse
            </div>
        </article>
    </section>

    <section class="content-grid">
        <article class="feature-card">
            <div class="section-heading">
                <div>
                    <div class="mono-eyebrow">EVALUATION SIGNALS</div>
                    <h2>Ringkasan note bertag</h2>
                </div>
                <a href="{{ route('notes.index') }}" class="button button-ghost-sm">Buka note</a>
            </div>

            <div class="signal-list">
                @forelse ($signals as $signal)
                    <div class="signal-row">
                        <span class="tag-chip">{{ $signal->tag }}</span>
                        <p>{{ $signal->aggregate }} note memakai tag ini.</p>
                    </div>
                @empty
                    <p class="empty-state">Belum ada data note bertag.</p>
                @endforelse
            </div>
        </article>

        <article class="feature-card">
            <div class="section-heading">
                <div>
                    <div class="mono-eyebrow">RECENT PROGRESS</div>
                    <h2>Update pembinaan terbaru</h2>
                </div>
                <a href="{{ route('progress.index') }}" class="button button-ghost-sm">Buka progress</a>
            </div>
            <div class="priority-list">
                @forelse ($recentProgress as $item)
                    <div class="priority-card">
                        <div class="priority-head">
                            <strong>{{ $item->member?->name ?: $item->area }}</strong>
                            <span>{{ $item->status }} · {{ $item->created_at->format('d M Y H:i') }}</span>
                        </div>
                        <p>{{ $item->summary }}</p>
                    </div>
                @empty
                    <p class="empty-state">Belum ada update progress.</p>
                @endforelse
            </div>
        </article>
    </section>

    <section class="content-grid">
        <article class="feature-card dark-callout">
            <div class="section-heading">
                <div>
                    <div class="mono-eyebrow">FINANCE SNAPSHOT</div>
                    <h2>Ringkasan keuangan bulan ini</h2>
                </div>
                <a href="{{ route('contributions.index') }}" class="button button-ghost-sm">Buka keuangan</a>
            </div>

            <div class="mini-stats-grid">
                <article class="mini-stat-card">
                    <span>Iuran outstanding</span>
                    <strong>Rp {{ number_format($financeSnapshot['outstanding_amount'], 0, ',', '.') }}</strong>
                    <p>{{ $financeSnapshot['outstanding_count'] }} tagihan belum lunas</p>
                </article>
                <article class="mini-stat-card">
                    <span>Pemasukan bulan ini</span>
                    <strong>Rp {{ number_format($financeSnapshot['month_income'], 0, ',', '.') }}</strong>
                    <p>Tercatat dari transaksi kas masuk</p>
                </article>
                <article class="mini-stat-card">
                    <span>Pengeluaran bulan ini</span>
                    <strong>Rp {{ number_format($financeSnapshot['month_expense'], 0, ',', '.') }}</strong>
                    <p>Tercatat dari transaksi kas keluar</p>
                </article>
            </div>
        </article>

        <article class="feature-card">
            <div class="section-heading">
                <div>
                    <div class="mono-eyebrow">AKSES PENGURUS</div>
                    <h2>Komposisi user login</h2>
                </div>
            </div>

            <div class="signal-list">
                @foreach ($operatorStats as $operator)
                    <div class="signal-row">
                        <span class="tag-chip">{{ $operator['label'] }}</span>
                        <p>{{ $operator['value'] }} akun login aktif pada kategori ini.</p>
                    </div>
                @endforeach
            </div>
        </article>
    </section>
@endsection
