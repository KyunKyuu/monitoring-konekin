@extends('layouts.dashboard', ['title' => 'Iuran'])

@section('dashboard-content')
    <section class="page-header">
        <div class="page-header-block">
            <div class="mono-eyebrow">KEUANGAN</div>
            <h1 class="page-title">Tagihan iuran member</h1>
            <p class="page-copy">Pantau tagihan per periode untuk membaca disiplin iuran dan kesiapan tanggung jawab anggota.</p>
            <div class="inline-metrics">
                <span class="metric-pill"><strong>{{ $contributions->total() }}</strong> total tagihan</span>
            </div>
        </div>
        <div class="hero-actions">
            <a href="{{ route('contributions.create') }}" class="button button-primary">Buat tagihan</a>
            <a href="{{ route('contribution-payments.create') }}" class="button button-secondary">Input pembayaran</a>
        </div>
    </section>

    <section class="feature-card">
        <div class="table-list">
            @forelse ($contributions as $contribution)
                <div class="table-row">
                    <div>
                        <strong>{{ $contribution->member->name }}</strong>
                        <p>Periode {{ $contribution->period_month }}/{{ $contribution->period_year }}</p>
                    </div>
                    <div>Tagihan Rp {{ number_format($contribution->amount_due, 0, ',', '.') }}</div>
                    <div>Bayar Rp {{ number_format($contribution->amount_paid, 0, ',', '.') }}</div>
                    <span class="status-pill">{{ $contribution->status }}</span>
                    <div class="row-actions">
                        <a href="{{ route('contributions.edit', $contribution) }}" class="table-action-link">Edit</a>
                        <form method="POST" action="{{ route('contributions.destroy', $contribution) }}" onsubmit="return confirm('Hapus tagihan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="button-link danger-link">Hapus</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="empty-state">Belum ada tagihan iuran.</p>
            @endforelse
        </div>
        <div class="pagination-wrap">{{ $contributions->links() }}</div>
    </section>
@endsection
