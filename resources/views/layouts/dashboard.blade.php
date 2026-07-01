@extends('layouts.app', ['bodyClass' => trim(($bodyClass ?? '').' dashboard-page')])

@section('content')
    @php
        $user = auth()->user();
    @endphp

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

            <div class="sidebar-search-wrap">
                <input type="search" class="sidebar-search" placeholder="Cari menu..." aria-label="Cari menu dashboard">
            </div>

            <nav class="sidebar-nav">
                <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <strong>Dashboard</strong>
                </a>

                @if ($user?->hasAnyRole(['super_admin', 'mentor']))
                    <div class="sidebar-group-label">Pembinaan</div>
                    <a href="{{ route('members.index') }}" class="sidebar-link {{ request()->routeIs('members.index') || request()->routeIs('members.create') || request()->routeIs('members.edit') || request()->routeIs('members.show') ? 'active' : '' }}"><strong>Member</strong></a>
                    <a href="{{ route('members.hierarchy') }}" class="sidebar-link {{ request()->routeIs('members.hierarchy') ? 'active' : '' }}"><strong>Kaka Tingkat</strong></a>
                    <a href="{{ route('activities.index') }}" class="sidebar-link {{ request()->routeIs('activities.*') || request()->routeIs('activities.calendar') ? 'active' : '' }}"><strong>Kegiatan</strong></a>
                    <a href="{{ route('notes.index') }}" class="sidebar-link {{ request()->routeIs('notes.*') ? 'active' : '' }}"><strong>Monitoring</strong></a>
                    <a href="{{ route('targets.index') }}" class="sidebar-link {{ request()->routeIs('targets.*') ? 'active' : '' }}"><strong>Target</strong></a>
                    <a href="{{ route('progress.index') }}" class="sidebar-link {{ request()->routeIs('progress.*') ? 'active' : '' }}"><strong>Progress</strong></a>
                @endif

                @if ($user?->hasAnyRole(['super_admin', 'pengurus_keuangan']))
                    <div class="sidebar-group-label">Keuangan</div>
                    <a href="{{ route('contributions.index') }}" class="sidebar-link {{ request()->routeIs('contributions.*') || request()->routeIs('contribution-payments.*') || request()->routeIs('cash-accounts.*') || request()->routeIs('cash-transactions.*') ? 'active' : '' }}"><strong>Keuangan</strong></a>
                @endif

                @if ($user?->hasRole('super_admin'))
                    <div class="sidebar-group-label">Pengaturan</div>
                    <a href="{{ route('operators.index') }}" class="sidebar-link {{ request()->routeIs('operators.*') ? 'active' : '' }}"><strong>Monitoring Pengurus</strong></a>
                    <a href="{{ route('ideal-positions.index') }}" class="sidebar-link {{ request()->routeIs('ideal-positions.*') || request()->routeIs('position-candidates.*') ? 'active' : '' }}"><strong>Pengurus Ideal</strong></a>
                    <a href="{{ route('categories.index') }}" class="sidebar-link {{ request()->routeIs('categories.*') || request()->routeIs('subcategories.*') ? 'active' : '' }}"><strong>Master Aktivitas</strong></a>
                @endif
            </nav>

            <div class="sidebar-footer">
                <div class="sidebar-help">
                    <span class="mono-eyebrow">ALUR KERJA</span>
                    @if ($user?->hasAnyRole(['super_admin', 'mentor']))
                        <p>Input kegiatan, tag member, lalu catat note atau progress.</p>
                    @else
                        <p>Buat tagihan, input pembayaran, lalu catat kas.</p>
                    @endif
                </div>
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
                <nav class="content-nav" aria-label="Navigasi halaman">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    @if (! request()->routeIs('dashboard'))
                        <span>/</span>
                        <strong>{{ $title ?? 'Halaman' }}</strong>
                    @endif
                </nav>
            </header>

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            @yield('dashboard-content')
        </main>
    </div>

    <script>
        document.querySelectorAll('.sidebar-search').forEach((input) => {
            input.addEventListener('input', () => {
                const query = input.value.trim().toLowerCase();
                document.querySelectorAll('.sidebar-link').forEach((link) => {
                    const isMatch = link.textContent.toLowerCase().includes(query);
                    link.hidden = query.length > 0 && !isMatch;
                });
            });
        });

        document.querySelectorAll('.table-list, .notion-list, .agenda-list, .priority-list, .signal-list').forEach((list) => {
            list.addEventListener('click', (event) => {
                const row = event.target.closest('a.table-row, a.notion-list-row, a.agenda-row, a.priority-card');
                if (row) {
                    row.classList.add('is-pressed');
                    window.setTimeout(() => row.classList.remove('is-pressed'), 160);
                }
            });
        });
    </script>
@endsection
