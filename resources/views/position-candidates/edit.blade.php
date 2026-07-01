@extends('layouts.dashboard', ['title' => 'Edit Kandidat Jabatan'])

@section('dashboard-content')
    <section class="page-header">
        <div>
            <div class="mono-eyebrow">PENGURUS IDEAL</div>
            <h1 class="page-title">Edit kandidat posisi</h1>
        </div>
    </section>

    <section class="feature-card">
        <form method="POST" action="{{ route('position-candidates.update', $positionCandidate) }}" class="form-grid">
            @csrf
            @method('PUT')
            <div class="field"><span>Posisi ideal</span>
                <select name="ideal_position_id" required>
                    @foreach ($positions as $position)
                        <option value="{{ $position->id }}" @selected(old('ideal_position_id', $positionCandidate->ideal_position_id) == $position->id)>{{ $position->function_name }} · {{ $position->position_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field"><span>Member kandidat</span>
                <select name="member_id" required>
                    @foreach ($members as $member)
                        <option value="{{ $member->id }}" @selected(old('member_id', $positionCandidate->member_id) == $member->id)>{{ $member->name }} · {{ $member->code }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field"><span>Status</span>
                <select name="status">
                    <option value="candidate" @selected(old('status', $positionCandidate->status) === 'candidate')>Candidate</option>
                    <option value="assigned" @selected(old('status', $positionCandidate->status) === 'assigned')>Assigned</option>
                    <option value="rejected" @selected(old('status', $positionCandidate->status) === 'rejected')>Rejected</option>
                </select>
            </div>
            <div class="field field-full"><span>Catatan</span><textarea name="notes" rows="5">{{ old('notes', $positionCandidate->notes) }}</textarea></div>
            <button type="submit" class="button button-primary">Update kandidat</button>
        </form>
    </section>
@endsection
