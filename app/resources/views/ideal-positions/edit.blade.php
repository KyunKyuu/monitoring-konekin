@extends('layouts.dashboard', ['title' => 'Edit Posisi Ideal'])

@section('dashboard-content')
    <section class="page-header">
        <div>
            <div class="mono-eyebrow">PENGURUS IDEAL</div>
            <h1 class="page-title">Edit posisi ideal</h1>
        </div>
    </section>

    <section class="feature-card">
        <form method="POST" action="{{ route('ideal-positions.update', $idealPosition) }}" class="form-grid">
            @csrf
            @method('PUT')
            <div class="field"><span>Fungsi</span><input type="text" name="function_name" value="{{ old('function_name', $idealPosition->function_name) }}" required></div>
            <div class="field"><span>Nama jabatan</span><input type="text" name="position_name" value="{{ old('position_name', $idealPosition->position_name) }}" required></div>
            <div class="field"><span>Jumlah kebutuhan</span><input type="number" min="1" name="required_count" value="{{ old('required_count', $idealPosition->required_count) }}" required></div>
            <div class="field"><span>Status</span>
                <select name="status">
                    <option value="open" @selected(old('status', $idealPosition->status) === 'open')>Open</option>
                    <option value="filled" @selected(old('status', $idealPosition->status) === 'filled')>Filled</option>
                    <option value="partial" @selected(old('status', $idealPosition->status) === 'partial')>Partial</option>
                </select>
            </div>
            <div class="field field-full"><span>Tujuan jabatan</span><textarea name="goal" rows="4">{{ old('goal', $idealPosition->goal) }}</textarea></div>
            <div class="field field-full"><span>Tanggung jawab</span><textarea name="responsibilities" rows="5">{{ old('responsibilities', $idealPosition->responsibilities) }}</textarea></div>
            <button type="submit" class="button button-primary">Update posisi</button>
        </form>
    </section>
@endsection
