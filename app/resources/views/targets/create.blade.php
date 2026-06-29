@extends('layouts.dashboard', ['title' => 'Tambah Target Pembinaan'])

@section('dashboard-content')
    <section class="page-header">
        <div>
            <div class="mono-eyebrow">TARGET PEMBINAAN</div>
            <h1 class="page-title">Tetapkan arah pembinaan</h1>
        </div>
    </section>

    <section class="feature-card">
        <form method="POST" action="{{ route('targets.store') }}" class="form-grid">
            @csrf
            <div class="field"><span>Member</span>
                <select name="member_id" required>
                    @foreach ($members as $member)
                        <option value="{{ $member->id }}">{{ $member->name }} · {{ $member->code }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field"><span>Fungsi</span><input type="text" name="function_name" value="{{ old('function_name') }}" placeholder="Keuangan" required></div>
            <div class="field"><span>Role / jabatan target</span><input type="text" name="role_name" value="{{ old('role_name') }}" placeholder="Calon Bendahara" required></div>
            <div class="field"><span>Prioritas</span>
                <select name="priority"><option value="low">Low</option><option value="medium">Medium</option><option value="high">High</option></select>
            </div>
            <div class="field"><span>Status</span>
                <select name="status"><option value="active">Active</option><option value="paused">Paused</option><option value="completed">Completed</option></select>
            </div>
            <div class="field field-full"><span>Tujuan pembinaan</span><textarea name="goal" rows="4">{{ old('goal') }}</textarea></div>
            <div class="field field-full"><span>Next action</span><textarea name="next_action" rows="4">{{ old('next_action') }}</textarea></div>
            <button type="submit" class="button button-primary">Simpan target</button>
        </form>
    </section>
@endsection
