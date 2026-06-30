@extends('layouts.app', ['bodyClass' => trim(($bodyClass ?? '').' dashboard-page')])

@section('content')
    @php($user = auth()->user())
    <div class="dashboard-shell sidebar-shell">
        <aside class="sidebar">
            <div class="sidebar-brand">
                <div class="brand-lockup">
                    <div class="brand-mark">K</div>
                    <div>
                        <div class="mono-eyebrow">KOMUNITAS INTERNAL</div>
                        <strong>Dashboard Pengurus</strong>
                    </div>
                </div>
            </div>

            <nav class="sidebar-nav">
                <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>

                @if ($user?->hasAnyRole(['super_admin', 'mentor']))
                    <div class="sidebar-group-label">Pembinaan</div>
                    <a href="{{ route('members.index') }}" class="sidebar-link {{ request()->routeIs('members.*') ? 'active' : '' }}">Member</a>
                    <a href="{{ route('members.hierarchy') }}" class="sidebar-link {{ request()->routeIs('members.hierarchy') ? 'active' : '' }}">Kaka Tingkat</a>
                    <a href="{{ route('activities.index') }}" class="sidebar-link {{ request()->routeIs('activities.*') || request()->routeIs('activities.calendar') ? 'active' : '' }}">Kegiatan</a>
                    <a href="{{ route('notes.index') }}" class="sidebar-link {{ request()->routeIs('notes.*') ? 'active' : '' }}">Monitoring</a>
                    <a href="{{ route('targets.index') }}" class="sidebar-link {{ request()->routeIs('targets.*') ? 'active' : '' }}">Target</a>
                    <a href="{{ route('progress.index') }}" class="sidebar-link {{ request()->routeIs('progress.*') ? 'active' : '' }}">Progress</a>
                @endif

                @if ($user?->hasAnyRole(['super_admin', 'pengurus_keuangan']))
                    <div class="sidebar-group-label">Keuangan</div>
                    <a href="{{ route('contributions.index') }}" class="sidebar-link {{ request()->routeIs('contributions.*') || request()->routeIs('contribution-payments.*') || request()->routeIs('cash-accounts.*') || request()->routeIs('cash-transactions.*') ? 'active' : '' }}">Keuangan</a>
                @endif

                @if ($user?->hasRole('super_admin'))
                    <div class="sidebar-group-label">Pengaturan</div>
                    <a href="{{ route('operators.index') }}" class="sidebar-link {{ request()->routeIs('operators.*') ? 'active' : '' }}">Monitoring Pengurus</a>
                    <a href="{{ route('ideal-positions.index') }}" class="sidebar-link {{ request()->routeIs('ideal-positions.*') || request()->routeIs('position-candidates.*') ? 'active' : '' }}">Pengurus Ideal</a>
                    <a href="{{ route('categories.index') }}" class="sidebar-link {{ request()->routeIs('categories.*') || request()->routeIs('subcategories.*') ? 'active' : '' }}">Master Aktivitas</a>
                @endif
            </nav>

            <div class="sidebar-footer">
                <div class="user-chip">
                    <span class="mono-eyebrow">ROLE</span>
                    <strong>{{ $user?->roleLabel() }}</strong>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="button button-ghost sidebar-logout">Logout</button>
                </form>
            </div>
        </aside>

        <main class="dashboard-main panel-main">
            <header class="content-topbar">
                <div>
                    <div class="mono-eyebrow">PANEL PENGURUS</div>
                    <h1 class="content-title">{{ $title ?? 'Dashboard' }}</h1>
                </div>
            </header>

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            @yield('dashboard-content')
        </main>
    </div>
@endsection
