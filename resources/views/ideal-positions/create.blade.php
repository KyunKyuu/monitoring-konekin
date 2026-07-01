@extends('layouts.dashboard', ['title' => 'Tambah Posisi Ideal'])

@section('dashboard-content')
    <section class="page-header">
        <div>
            <div class="mono-eyebrow">PENGURUS IDEAL</div>
            <h1 class="page-title">Tambah posisi ideal</h1>
        </div>
    </section>

    <section class="feature-card">
        <form method="POST" action="{{ route('ideal-positions.store') }}" class="form-grid">
            @csrf
            <div class="field"><span>Fungsi</span><input type="text" name="function_name" required></div>
            <div class="field"><span>Nama jabatan</span><input type="text" name="position_name" required></div>
            <div class="field"><span>Jumlah kebutuhan</span><input type="number" min="1" name="required_count" value="1" required></div>
            <div class="field"><span>Status</span>
                <select name="status"><option value="open">Open</option><option value="filled">Filled</option><option value="partial">Partial</option></select>
            </div>
            <div class="field field-full"><span>Tujuan jabatan</span><textarea name="goal" rows="4"></textarea></div>
            <div class="field field-full"><span>Tanggung jawab</span><textarea name="responsibilities" rows="5"></textarea></div>
            <button type="submit" class="button button-primary">Simpan posisi</button>
        </form>
    </section>
@endsection
