@extends('layouts.dashboard', ['title' => 'Edit Transaksi Kas'])

@section('dashboard-content')
    <section class="page-header">
        <div>
            <div class="mono-eyebrow">KEUANGAN</div>
            <h1 class="page-title">Edit transaksi kas</h1>
        </div>
    </section>

    <section class="feature-card">
        <form method="POST" action="{{ route('cash-transactions.update', $cashTransaction) }}" class="form-grid">
            @csrf
            @method('PUT')
            <div class="field"><span>Akun kas</span>
                <select name="cash_account_id" required>
                    @foreach ($cashAccounts as $account)
                        <option value="{{ $account->id }}" @selected(old('cash_account_id', $cashTransaction->cash_account_id) == $account->id)>{{ $account->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field"><span>Kegiatan terkait</span>
                <select name="activity_id">
                    <option value="">Tanpa kegiatan</option>
                    @foreach ($activities as $activity)
                        <option value="{{ $activity->id }}" @selected(old('activity_id', $cashTransaction->activity_id) == $activity->id)>{{ $activity->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field"><span>Tipe</span>
                <select name="type">
                    <option value="income" @selected(old('type', $cashTransaction->type) === 'income')>Income</option>
                    <option value="expense" @selected(old('type', $cashTransaction->type) === 'expense')>Expense</option>
                </select>
            </div>
            <div class="field"><span>Nominal</span><input type="number" step="0.01" name="amount" value="{{ old('amount', $cashTransaction->amount) }}" required></div>
            <div class="field"><span>Tanggal transaksi</span><input type="date" name="transaction_date" value="{{ old('transaction_date', $cashTransaction->transaction_date->toDateString()) }}" required></div>
            <div class="field"><span>Kategori</span><input type="text" name="category" value="{{ old('category', $cashTransaction->category) }}"></div>
            <div class="field field-full"><span>Deskripsi</span><textarea name="description" rows="4" required>{{ old('description', $cashTransaction->description) }}</textarea></div>
            <button type="submit" class="button button-primary">Update transaksi</button>
        </form>
    </section>
@endsection
