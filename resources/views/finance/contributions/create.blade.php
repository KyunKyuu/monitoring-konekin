@extends('layouts.dashboard', ['title' => 'Buat Tagihan Iuran'])

@section('dashboard-content')
    <section class="page-header">
        <div>
            <div class="mono-eyebrow">KEUANGAN</div>
            <h1 class="page-title">Buat tagihan iuran</h1>
        </div>
    </section>

    <section class="feature-card">
        <form method="POST" action="{{ route('contributions.store') }}" class="form-grid">
            @csrf
            <div class="field"><span>Member</span>
                <select name="member_id" required>
                    @foreach ($members as $member)
                        <option value="{{ $member->id }}">{{ $member->name }} · {{ $member->code }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field"><span>Bulan</span><input type="number" min="1" max="12" name="period_month" value="{{ old('period_month', now()->month) }}" required></div>
            <div class="field"><span>Tahun</span><input type="number" name="period_year" value="{{ old('period_year', now()->year) }}" required></div>
            <div class="field"><span>Nominal tagihan</span><input type="number" step="0.01" name="amount_due" value="{{ old('amount_due', 50000) }}" required></div>
            <div class="field"><span>Jatuh tempo</span><input type="date" name="due_date" value="{{ old('due_date') }}"></div>
            <button type="submit" class="button button-primary">Simpan tagihan</button>
        </form>
    </section>
@endsection
