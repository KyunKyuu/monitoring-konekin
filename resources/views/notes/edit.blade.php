@extends('layouts.dashboard', ['title' => 'Edit Note'])

@section('dashboard-content')
    <section class="page-header">
        <div>
            <div class="mono-eyebrow">MONITORING</div>
            <h1 class="page-title">Edit note evaluasi</h1>
        </div>
    </section>

    <section class="feature-card">
        <form method="POST" action="{{ route('notes.update', $note) }}" class="form-grid">
            @csrf
            @method('PUT')
            <div class="field"><span>Member</span>
                <select name="member_id" required>
                    @foreach ($members as $member)
                        <option value="{{ $member->id }}" @selected(old('member_id', $note->member_id) == $member->id)>{{ $member->name }} · {{ $member->code }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field"><span>Kegiatan terkait</span>
                <select name="activity_id">
                    <option value="">Tanpa kegiatan</option>
                    @foreach ($activities as $activity)
                        <option value="{{ $activity->id }}" @selected(old('activity_id', $note->activity_id) == $activity->id)>{{ $activity->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field"><span>Tag</span><input type="text" name="tag" value="{{ old('tag', $note->tag) }}" required></div>
            <div class="field"><span>Level</span>
                <select name="level">
                    <option value="info" @selected(old('level', $note->level) === 'info')>Info</option>
                    <option value="attention" @selected(old('level', $note->level) === 'attention')>Attention</option>
                    <option value="urgent" @selected(old('level', $note->level) === 'urgent')>Urgent</option>
                </select>
            </div>
            <div class="field"><span>Status tindak lanjut</span>
                <select name="follow_up_status">
                    <option value="open" @selected(old('follow_up_status', $note->follow_up_status) === 'open')>Open</option>
                    <option value="monitoring" @selected(old('follow_up_status', $note->follow_up_status) === 'monitoring')>Monitoring</option>
                    <option value="closed" @selected(old('follow_up_status', $note->follow_up_status) === 'closed')>Closed</option>
                </select>
            </div>
            <div class="field field-full"><span>Isi note</span><textarea name="content" rows="5" required>{{ old('content', $note->content) }}</textarea></div>
            <div class="field field-full"><span>Tindak lanjut</span><textarea name="follow_up_action" rows="4">{{ old('follow_up_action', $note->follow_up_action) }}</textarea></div>
            <button type="submit" class="button button-primary">Update note</button>
        </form>
    </section>
@endsection
