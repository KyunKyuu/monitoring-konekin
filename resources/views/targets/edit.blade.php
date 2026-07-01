@extends('layouts.dashboard', ['title' => 'Edit Target Pembinaan'])

@section('dashboard-content')
    <section class="page-header">
        <div>
            <div class="mono-eyebrow">TARGET PEMBINAAN</div>
            <h1 class="page-title">Edit arah pembinaan</h1>
        </div>
    </section>

    <section class="feature-card">
        <form method="POST" action="{{ route('targets.update', $target) }}" class="form-grid">
            @csrf
            @method('PUT')
            <div class="field"><span>Member</span>
                <select name="member_id" required>
                    @foreach ($members as $member)
                        <option value="{{ $member->id }}" @selected(old('member_id', $target->member_id) == $member->id)>{{ $member->name }} · {{ $member->code }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field"><span>Fungsi</span><input type="text" name="function_name" value="{{ old('function_name', $target->function_name) }}" required></div>
            <div class="field"><span>Role / jabatan target</span><input type="text" name="role_name" value="{{ old('role_name', $target->role_name) }}" required></div>
            <div class="field"><span>Prioritas</span>
                <select name="priority">
                    <option value="low" @selected(old('priority', $target->priority) === 'low')>Low</option>
                    <option value="medium" @selected(old('priority', $target->priority) === 'medium')>Medium</option>
                    <option value="high" @selected(old('priority', $target->priority) === 'high')>High</option>
                </select>
            </div>
            <div class="field"><span>Status</span>
                <select name="status">
                    <option value="active" @selected(old('status', $target->status) === 'active')>Active</option>
                    <option value="paused" @selected(old('status', $target->status) === 'paused')>Paused</option>
                    <option value="completed" @selected(old('status', $target->status) === 'completed')>Completed</option>
                </select>
            </div>
            <div class="field field-full"><span>Tujuan pembinaan</span><textarea name="goal" rows="4">{{ old('goal', $target->goal) }}</textarea></div>
            <div class="field field-full"><span>Next action</span><textarea name="next_action" rows="4">{{ old('next_action', $target->next_action) }}</textarea></div>
            <button type="submit" class="button button-primary">Update target</button>
        </form>
    </section>
@endsection
