@extends('layouts.dashboard', ['title' => 'Monitoring Note'])

@section('dashboard-content')
    <section class="page-header">
        <div>
            <div class="mono-eyebrow">MONITORING</div>
            <h1 class="page-title">Note evaluasi anggota</h1>
        </div>
        <a href="{{ route('notes.create') }}" class="button button-primary">Tambah note</a>
    </section>

    <section class="feature-card">
        <form method="GET" class="toolbar">
            <select name="tag">
                <option value="">Semua tag</option>
                @foreach ($tags as $item)
                    <option value="{{ $item }}" @selected($tag === $item)>{{ $item }}</option>
                @endforeach
            </select>
            <button type="submit" class="button button-ghost-sm">Filter</button>
        </form>

        <div class="priority-list">
            @forelse ($notes as $note)
                <div class="priority-card">
                    <div class="priority-head">
                        <strong>{{ $note->member->name }} · {{ $note->tag }}</strong>
                        <span>{{ $note->author->name }} · {{ $note->follow_up_status }}</span>
                    </div>
                    <p>{{ $note->content }}</p>
                    <div class="row-actions">
                        <a href="{{ route('notes.edit', $note) }}" class="table-action-link">Edit</a>
                        <form method="POST" action="{{ route('notes.destroy', $note) }}" onsubmit="return confirm('Hapus note ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="button-link danger-link">Hapus</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="empty-state">Belum ada note.</p>
            @endforelse
        </div>

        <div class="pagination-wrap">{{ $notes->links() }}</div>
    </section>
@endsection
