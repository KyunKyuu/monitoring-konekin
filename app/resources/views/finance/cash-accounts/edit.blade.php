@extends('layouts.dashboard', ['title' => 'Edit Akun Kas'])

@section('dashboard-content')
    <section class="page-header">
        <div>
            <div class="mono-eyebrow">KEUANGAN</div>
            <h1 class="page-title">Edit akun kas</h1>
        </div>
    </section>

    <section class="feature-card">
        <form method="POST" action="{{ route('cash-accounts.update', $cashAccount) }}" class="form-grid">
            @csrf
            @method('PUT')
            <div class="field"><span>Kode kas</span><input type="text" name="code" value="{{ old('code', $cashAccount->code) }}" required></div>
            <div class="field"><span>Nama kas</span><input type="text" name="name" value="{{ old('name', $cashAccount->name) }}" required></div>
            <div class="field"><span>Status aktif</span>
                <select name="is_active">
                    <option value="1" @selected((string) old('is_active', (int) $cashAccount->is_active) === '1')>Active</option>
                    <option value="0" @selected((string) old('is_active', (int) $cashAccount->is_active) === '0')>Inactive</option>
                </select>
            </div>
            <div class="field field-full"><span>Deskripsi</span><textarea name="description" rows="4">{{ old('description', $cashAccount->description) }}</textarea></div>
            <button type="submit" class="button button-primary">Update akun kas</button>
        </form>
    </section>
@endsection
