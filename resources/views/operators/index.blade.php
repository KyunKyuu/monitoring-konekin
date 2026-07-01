@extends('layouts.dashboard', ['title' => 'Monitoring Pengurus'])

@section('dashboard-content')
    <section class="page-header">
        <div class="page-header-block">
            <div class="mono-eyebrow">SUPER ADMIN</div>
            <h1 class="page-title">Monitoring pengurus</h1>
            <p class="page-copy">Pantau pengurus yang aktif menginput kegiatan, note, progress, pembayaran, dan transaksi kas bulan ini.</p>
            <div class="inline-metrics">
                <span class="metric-pill"><strong>{{ $operators->count() }}</strong> total akun</span>
                <span class="metric-pill"><strong>{{ $activeOperators }}</strong> pernah aktif</span>
                <span class="metric-pill"><strong>{{ $inactiveOperators }}</strong> belum ada aktivitas</span>
            </div>
        </div>
    </section>

    <section class="notion-section">
        <div class="notion-table">
            <div class="notion-table-head operator-grid">
                <span>Pengurus</span>
                <span>Kegiatan</span>
                <span>Monitoring</span>
                <span>Keuangan</span>
                <span>Aktivitas terakhir</span>
            </div>

            @forelse ($operators as $operator)
                <div class="notion-table-row operator-grid">
                    <div>
                        <strong>{{ $operator->name }}</strong>
                        <p>{{ $operator->username }} · {{ $operator->roleLabel() }}</p>
                    </div>
                    <div>
                        <span class="status-pill">{{ $operator->activities_this_month_count }} kegiatan</span>
                    </div>
                    <div class="operator-stack">
                        <span>{{ $operator->notes_this_month_count }} note</span>
                        <span>{{ $operator->progress_this_month_count }} progress</span>
                    </div>
                    <div class="operator-stack">
                        <span>{{ $operator->payments_this_month_count }} pembayaran</span>
                        <span>{{ $operator->cash_transactions_this_month_count }} kas</span>
                    </div>
                    <div>
                        <strong>{{ $operator->last_operator_activity_label }}</strong>
                        <p>{{ $operator->last_operator_activity_at ? $operator->last_operator_activity_at->diffForHumans() : 'Belum ada tanggal aktivitas' }}</p>
                    </div>
                </div>
            @empty
                <p class="empty-state">Belum ada akun pengurus.</p>
            @endforelse
        </div>
    </section>
@endsection
