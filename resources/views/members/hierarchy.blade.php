@extends('layouts.dashboard', ['title' => 'Struktur Kaka Tingkat'])

@section('dashboard-content')
    <section class="page-header">
        <div class="page-header-block">
            <div class="mono-eyebrow">PENDEKATAN ANGGOTA</div>
            <h1 class="page-title">Struktur kaka tingkat</h1>
            <p class="page-copy">Lihat siapa membawahi siapa untuk menjaga pendekatan, pembinaan, dan keterhubungan anggota.</p>
            <div class="inline-metrics">
                <span class="metric-pill"><strong>{{ $leaders->count() }}</strong> kaka tingkat aktif</span>
                <span class="metric-pill"><strong>{{ $independentMembers->count() }}</strong> anggota mandiri</span>
            </div>
        </div>
        <div class="hero-actions">
            <a href="{{ route('members.index') }}" class="button button-secondary">Master member</a>
            <a href="{{ route('members.create') }}" class="button button-primary">Tambah member</a>
        </div>
    </section>

    <section class="content-grid content-grid-wide-left hierarchy-workspace">
        <article class="feature-card hierarchy-panel">
            <div class="section-heading">
                <div>
                    <div class="mono-eyebrow">STRUKTUR UTAMA</div>
                    <h2>Pohon pendekatan</h2>
                </div>
            </div>

            <div class="compact-tree">
                @forelse ($leaders as $leader)
                    <div class="tree-group">
                        <div class="tree-row tree-root">
                            <div class="tree-connector"></div>
                            <div class="tree-person">
                                <strong>{{ $leader->name }}</strong>
                                <span>{{ $leader->code }} · {{ $leader->target_role ?: 'Kaka tingkat' }}</span>
                            </div>
                            <form method="POST" action="{{ route('members.hierarchy.update', $leader) }}" class="tree-assign-form">
                                @csrf
                                @method('PATCH')
                                <select name="kaka_tingkat_id">
                                    <option value="">Tanpa kaka tingkat</option>
                                    @foreach ($allMembers as $option)
                                        @if ($option->id !== $leader->id)
                                            <option value="{{ $option->id }}" @selected($leader->kaka_tingkat_id === $option->id)>{{ $option->name }} · {{ $option->code }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <button type="submit" class="tree-save-button">Simpan</button>
                            </form>
                        </div>

                        <div class="tree-children">
                            @foreach ($leader->adikTingkat as $adik)
                                <div class="tree-branch">
                                    <div class="tree-row">
                                        <div class="tree-connector"></div>
                                        <div class="tree-person">
                                            <strong>{{ $adik->name }}</strong>
                                            <span>{{ $adik->code }} · {{ $adik->target_role ?: 'Adik tingkat' }}</span>
                                        </div>
                                        <form method="POST" action="{{ route('members.hierarchy.update', $adik) }}" class="tree-assign-form">
                                            @csrf
                                            @method('PATCH')
                                            <select name="kaka_tingkat_id">
                                                <option value="">Tanpa kaka tingkat</option>
                                                @foreach ($allMembers as $option)
                                                    @if ($option->id !== $adik->id)
                                                        <option value="{{ $option->id }}" @selected($adik->kaka_tingkat_id === $option->id)>{{ $option->name }} · {{ $option->code }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            <button type="submit" class="tree-save-button">Simpan</button>
                                        </form>
                                    </div>

                                    @if ($adik->adikTingkat->isNotEmpty())
                                        <div class="tree-subchildren">
                                            @foreach ($adik->adikTingkat as $subadik)
                                                <div class="tree-row tree-row-small">
                                                    <div class="tree-connector"></div>
                                                    <div class="tree-person">
                                                        <strong>{{ $subadik->name }}</strong>
                                                        <span>{{ $subadik->code }} · {{ $subadik->target_role ?: 'Adik tingkat' }}</span>
                                                    </div>
                                                    <form method="POST" action="{{ route('members.hierarchy.update', $subadik) }}" class="tree-assign-form">
                                                        @csrf
                                                        @method('PATCH')
                                                        <select name="kaka_tingkat_id">
                                                            <option value="">Tanpa kaka tingkat</option>
                                                            @foreach ($allMembers as $option)
                                                                @if ($option->id !== $subadik->id)
                                                                    <option value="{{ $option->id }}" @selected($subadik->kaka_tingkat_id === $option->id)>{{ $option->name }} · {{ $option->code }}</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                        <button type="submit" class="tree-save-button">Simpan</button>
                                                    </form>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="empty-state">Belum ada struktur kaka tingkat yang terbentuk.</p>
                @endforelse
            </div>
        </article>

        <article class="feature-card hierarchy-panel">
            <div class="section-heading">
                <div>
                    <div class="mono-eyebrow">ANGGOTA MANDIRI</div>
                    <h2>Belum terhubung</h2>
                </div>
            </div>

            <div class="compact-tree">
                @forelse ($independentMembers as $member)
                    <div class="tree-row tree-row-independent">
                        <div class="tree-person">
                            <strong>{{ $member->name }}</strong>
                            <span>{{ $member->code }} · {{ $member->target_role ?: 'Belum ada target' }}</span>
                        </div>
                        <form method="POST" action="{{ route('members.hierarchy.update', $member) }}" class="tree-assign-form">
                            @csrf
                            @method('PATCH')
                            <select name="kaka_tingkat_id">
                                <option value="">Tanpa kaka tingkat</option>
                                @foreach ($allMembers as $option)
                                    @if ($option->id !== $member->id)
                                        <option value="{{ $option->id }}" @selected($member->kaka_tingkat_id === $option->id)>{{ $option->name }} · {{ $option->code }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <button type="submit" class="tree-save-button">Simpan</button>
                        </form>
                    </div>
                @empty
                    <p class="empty-state">Semua anggota sudah masuk ke struktur atau menjadi kaka tingkat.</p>
                @endforelse
            </div>
        </article>
    </section>
@endsection
