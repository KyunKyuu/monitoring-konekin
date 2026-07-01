@extends('layouts.dashboard', ['title' => 'Tambah Transaksi Kas'])

@section('dashboard-content')
    <section class="page-header">
        <div>
            <div class="mono-eyebrow">KEUANGAN</div>
            <h1 class="page-title">Catat transaksi kas</h1>
        </div>
    </section>

    <section class="feature-card">
        <form method="POST" action="{{ route('cash-transactions.store') }}" class="form-grid">
            @csrf
            <div class="field"><span>Akun kas</span>
                <select name="cash_account_id" required>
                    @foreach ($cashAccounts as $account)
                        <option value="{{ $account->id }}">{{ $account->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field"><span>Kegiatan terkait</span>
                <select name="activity_id">
                    <option value="">Tanpa kegiatan</option>
                    @foreach ($activities as $activity)
                        <option value="{{ $activity->id }}">{{ $activity->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field"><span>Tipe</span>
                <select name="type"><option value="income">Income</option><option value="expense">Expense</option></select>
            </div>
            <div class="field"><span>Nominal</span><input type="number" step="0.01" name="amount" required></div>
            <div class="field"><span>Tanggal transaksi</span><input type="date" name="transaction_date" value="{{ now()->toDateString() }}" required></div>
            <div class="field"><span>Kategori</span><input type="text" name="category" placeholder="Operasional kegiatan"></div>
            <div class="field field-full"><span>Deskripsi</span><textarea name="description" rows="4" required>{{ old('description') }}</textarea></div>
            <button type="submit" class="button button-primary">Simpan transaksi</button>
        </form>
    </section>
@endsection
