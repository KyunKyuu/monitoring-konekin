@extends('layouts.dashboard', ['title' => 'Pembayaran Iuran'])

@section('dashboard-content')
    <section class="page-header">
        <div>
            <div class="mono-eyebrow">KEUANGAN</div>
            <h1 class="page-title">Input pembayaran iuran</h1>
        </div>
    </section>

    <section class="feature-card">
        <form method="POST" action="{{ route('contribution-payments.store') }}" class="form-grid">
            @csrf
            <div class="field"><span>Tagihan iuran</span>
                <select name="contribution_id" required>
                    @foreach ($contributions as $contribution)
                        <option value="{{ $contribution->id }}">{{ $contribution->member->name }} · {{ $contribution->period_month }}/{{ $contribution->period_year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field"><span>Akun kas</span>
                <select name="cash_account_id">
                    <option value="">Tanpa catat ke kas</option>
                    @foreach ($cashAccounts as $account)
                        <option value="{{ $account->id }}">{{ $account->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field"><span>Nominal bayar</span><input type="number" step="0.01" name="amount" required></div>
            <div class="field"><span>Tanggal bayar</span><input type="date" name="paid_on" value="{{ now()->toDateString() }}" required></div>
            <div class="field"><span>Metode bayar</span><input type="text" name="payment_method" value="{{ old('payment_method') }}" placeholder="Tunai / Transfer"></div>
            <div class="field field-full"><span>Catatan</span><textarea name="notes" rows="4">{{ old('notes') }}</textarea></div>
            <button type="submit" class="button button-primary">Simpan pembayaran</button>
        </form>
    </section>
@endsection
