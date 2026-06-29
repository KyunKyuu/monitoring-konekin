@extends('layouts.app')

@section('content')
    <div class="dashboard-shell">
        @php($user = auth()->user())
        <header class="topbar">
            <div class="brand-lockup">
                <div class="brand-mark">K</div>
                <div>
                    <div class="mono-eyebrow">KOMUNITAS INTERNAL</div>
                    <strong>Dashboard Pengurus</strong>
                </div>
            </div>

            <nav class="topbar-nav">
                <a href="{{ route('dashboard') }}" class="nav-chip {{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
                @if ($user?->hasAnyRole(['super_admin', 'mentor']))
                    <a href="{{ route('members.index') }}" class="nav-chip {{ request()->routeIs('members.*') ? 'active' : '' }}">Member</a>
                    <a href="{{ route('activities.index') }}" class="nav-chip {{ request()->routeIs('activities.*') || request()->routeIs('activities.calendar') ? 'active' : '' }}">Kegiatan</a>
                    <a href="{{ route('notes.index') }}" class="nav-chip {{ request()->routeIs('notes.*') ? 'active' : '' }}">Monitoring</a>
                    <a href="{{ route('targets.index') }}" class="nav-chip {{ request()->routeIs('targets.*') ? 'active' : '' }}">Target</a>
                    <a href="{{ route('progress.index') }}" class="nav-chip {{ request()->routeIs('progress.*') ? 'active' : '' }}">Progress</a>
                @endif
                @if ($user?->hasAnyRole(['super_admin', 'pengurus_keuangan']))
                    <a href="{{ route('contributions.index') }}" class="nav-chip {{ request()->routeIs('contributions.*') || request()->routeIs('contribution-payments.*') || request()->routeIs('cash-accounts.*') || request()->routeIs('cash-transactions.*') ? 'active' : '' }}">Keuangan</a>
                @endif
                @if ($user?->hasRole('super_admin'))
                    <a href="{{ route('ideal-positions.index') }}" class="nav-chip {{ request()->routeIs('ideal-positions.*') || request()->routeIs('position-candidates.*') ? 'active' : '' }}">Pengurus Ideal</a>
                    <a href="{{ route('categories.index') }}" class="nav-chip {{ request()->routeIs('categories.*') || request()->routeIs('subcategories.*') ? 'active' : '' }}">Master Aktivitas</a>
                @endif
            </nav>

            <div class="topbar-meta">
                <div class="user-chip">
                    <span class="mono-eyebrow">ROLE</span>
                    <strong>{{ $user?->roleLabel() }}</strong>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="button button-ghost">Logout</button>
                </form>
            </div>
        </header>

        <main class="dashboard-main">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            @yield('dashboard-content')
        </main>
    </div>
@endsection
