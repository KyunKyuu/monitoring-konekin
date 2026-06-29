@extends('layouts.dashboard', ['title' => 'Edit Progress'])

@section('dashboard-content')
    <section class="page-header">
        <div>
            <div class="mono-eyebrow">PROGRESS PEMBINAAN</div>
            <h1 class="page-title">Edit update perkembangan</h1>
        </div>
    </section>

    <section class="feature-card">
        <form method="POST" action="{{ route('progress.update', $progress) }}" class="form-grid">
            @csrf
            @method('PUT')
            <div class="field"><span>Member</span>
                <select name="member_id" required>
                    @foreach ($members as $member)
                        <option value="{{ $member->id }}" @selected(old('member_id', $progress->member_id) == $member->id)>{{ $member->name }} · {{ $member->code }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field"><span>Target pembinaan</span>
                <select name="development_target_id">
                    <option value="">Tanpa target spesifik</option>
                    @foreach ($targets as $target)
                        <option value="{{ $target->id }}" @selected(old('development_target_id', $progress->development_target_id) == $target->id)>{{ $target->member->name }} · {{ $target->role_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field"><span>Kegiatan terkait</span>
                <select name="activity_id">
                    <option value="">Tanpa kegiatan</option>
                    @foreach ($activities as $activity)
                        <option value="{{ $activity->id }}" @selected(old('activity_id', $progress->activity_id) == $activity->id)>{{ $activity->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field"><span>Area</span><input type="text" name="area" value="{{ old('area', $progress->area) }}" required></div>
            <div class="field"><span>Tahap</span><input type="text" name="stage" value="{{ old('stage', $progress->stage) }}"></div>
            <div class="field"><span>Status</span>
                <select name="status">
                    <option value="on_track" @selected(old('status', $progress->status) === 'on_track')>On track</option>
                    <option value="needs_attention" @selected(old('status', $progress->status) === 'needs_attention')>Needs attention</option>
                    <option value="completed" @selected(old('status', $progress->status) === 'completed')>Completed</option>
                </select>
            </div>
            <div class="field field-full"><span>Ringkasan progress</span><textarea name="summary" rows="5" required>{{ old('summary', $progress->summary) }}</textarea></div>
            <button type="submit" class="button button-primary">Update progress</button>
        </form>
    </section>
@endsection
