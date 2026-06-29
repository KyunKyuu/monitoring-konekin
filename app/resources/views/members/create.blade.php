@extends('layouts.dashboard', ['title' => 'Tambah Member'])

@section('dashboard-content')
    <section class="page-header">
        <div>
            <div class="mono-eyebrow">MASTER MEMBER</div>
            <h1 class="page-title">Tambah member baru</h1>
        </div>
    </section>

    <section class="feature-card">
        <form method="POST" action="{{ route('members.store') }}" class="form-grid">
            @csrf
            @include('members.partials.form')
            <button type="submit" class="button button-primary">Simpan member</button>
        </form>
    </section>
@endsection
