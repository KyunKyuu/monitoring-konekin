@extends('layouts.dashboard', ['title' => 'Transaksi Kas'])

@section('dashboard-content')
    <section class="page-header">
        <div class="page-header-block">
            <div class="mono-eyebrow">KEUANGAN</div>
            <h1 class="page-title">Mutasi kas masuk dan keluar</h1>
            <p class="page-copy">Lacak pemasukan dan pengeluaran kas yang dipakai untuk agenda maupun kebutuhan operasional.</p>
            <div class="inline-metrics">
                <span class="metric-pill"><strong>{{ $transactions->total() }}</strong> total transaksi</span>
            </div>
        </div>
        <a href="{{ route('cash-transactions.create') }}" class="button button-primary">Tambah transaksi</a>
    </section>

    <section class="feature-card">
        <div class="table-list">
            @forelse ($transactions as $transaction)
                <div class="table-row">
                    <div>
                        <strong>{{ $transaction->cashAccount->name }}</strong>
                        <p>{{ $transaction->transaction_date->format('d M Y') }}</p>
                    </div>
                    <div>{{ $transaction->category ?: '-' }}</div>
                    <div>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</div>
                    <span class="status-pill">{{ $transaction->type }}</span>
                    <div class="row-actions">
                        <a href="{{ route('cash-transactions.edit', $transaction) }}" class="table-action-link">Edit</a>
                        <form method="POST" action="{{ route('cash-transactions.destroy', $transaction) }}" onsubmit="return confirm('Hapus transaksi ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="button-link danger-link">Hapus</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="empty-state">Belum ada transaksi kas.</p>
            @endforelse
        </div>
        <div class="pagination-wrap">{{ $transactions->links() }}</div>
    </section>
@endsection
