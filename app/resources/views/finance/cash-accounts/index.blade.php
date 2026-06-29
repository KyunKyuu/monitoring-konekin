@extends('layouts.dashboard', ['title' => 'Akun Kas'])

@section('dashboard-content')
    <section class="page-header">
        <div>
            <div class="mono-eyebrow">KEUANGAN</div>
            <h1 class="page-title">Akun kas komunitas</h1>
        </div>
        <a href="{{ route('cash-accounts.create') }}" class="button button-primary">Tambah akun kas</a>
    </section>

    <section class="feature-card">
        <div class="table-list">
            @forelse ($accounts as $account)
                <div class="table-row">
                    <div>
                        <strong>{{ $account->name }}</strong>
                        <p>{{ $account->code }}</p>
                    </div>
                    <div>{{ $account->description ?: '-' }}</div>
                    <div>{{ $account->transactions_count }} transaksi</div>
                    <span class="status-pill">{{ $account->is_active ? 'active' : 'inactive' }}</span>
                    <div class="row-actions">
                        <a href="{{ route('cash-accounts.edit', $account) }}" class="table-action-link">Edit</a>
                        <form method="POST" action="{{ route('cash-accounts.destroy', $account) }}" onsubmit="return confirm('Hapus akun kas ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="button-link danger-link">Hapus</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="empty-state">Belum ada akun kas.</p>
            @endforelse
        </div>
    </section>
@endsection
