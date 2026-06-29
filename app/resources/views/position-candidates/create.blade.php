@extends('layouts.dashboard', ['title' => 'Tambah Kandidat Jabatan'])

@section('dashboard-content')
    <section class="page-header">
        <div>
            <div class="mono-eyebrow">PENGURUS IDEAL</div>
            <h1 class="page-title">Tambah kandidat posisi</h1>
        </div>
    </section>

    <section class="feature-card">
        <form method="POST" action="{{ route('position-candidates.store') }}" class="form-grid">
            @csrf
            <div class="field"><span>Posisi ideal</span>
                <select name="ideal_position_id" required>
                    @foreach ($positions as $position)
                        <option value="{{ $position->id }}">{{ $position->function_name }} · {{ $position->position_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field"><span>Member kandidat</span>
                <select name="member_id" required>
                    @foreach ($members as $member)
                        <option value="{{ $member->id }}">{{ $member->name }} · {{ $member->code }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field"><span>Status</span>
                <select name="status"><option value="candidate">Candidate</option><option value="assigned">Assigned</option><option value="rejected">Rejected</option></select>
            </div>
            <div class="field field-full"><span>Catatan</span><textarea name="notes" rows="5"></textarea></div>
            <button type="submit" class="button button-primary">Simpan kandidat</button>
        </form>
    </section>
@endsection
