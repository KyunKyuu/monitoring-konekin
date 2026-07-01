@extends('layouts.dashboard', ['title' => 'Edit Member'])

@section('dashboard-content')
    <section class="page-header">
        <div>
            <div class="mono-eyebrow">MASTER MEMBER</div>
            <h1 class="page-title">Edit member</h1>
        </div>
    </section>

    <section class="feature-card">
        <form method="POST" action="{{ route('members.update', $member) }}" class="form-grid">
            @csrf
            @method('PUT')
            @include('members.partials.form', ['member' => $member])
            <button type="submit" class="button button-primary">Update member</button>
        </form>
    </section>
@endsection
