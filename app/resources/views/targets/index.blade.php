@extends('layouts.dashboard', ['title' => 'Target Pembinaan'])

@section('dashboard-content')
    <section class="page-header">
        <div>
            <div class="mono-eyebrow">TARGET PEMBINAAN</div>
            <h1 class="page-title">Arah pengembangan member</h1>
        </div>
        <a href="{{ route('targets.create') }}" class="button button-primary">Tambah target</a>
    </section>

    <section class="feature-card">
        <div class="priority-list">
            @forelse ($targets as $target)
                <div class="priority-card">
                    <div class="priority-head">
                        <strong>{{ $target->member->name }} · {{ $target->role_name }}</strong>
                        <span>{{ $target->function_name }} · {{ $target->priority }} · {{ $target->status }}</span>
                    </div>
                    <p>{{ $target->next_action ?: ($target->goal ?: 'Belum ada next action.') }}</p>
                    <div class="row-actions">
                        <a href="{{ route('targets.edit', $target) }}" class="table-action-link">Edit</a>
                        <form method="POST" action="{{ route('targets.destroy', $target) }}" onsubmit="return confirm('Hapus target ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="button-link danger-link">Hapus</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="empty-state">Belum ada target pembinaan.</p>
            @endforelse
        </div>

        <div class="pagination-wrap">{{ $targets->links() }}</div>
    </section>
@endsection
