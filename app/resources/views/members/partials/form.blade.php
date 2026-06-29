<div class="field">
    <span>Kode member</span>
    <input type="text" name="code" value="{{ old('code', $member->code ?? '') }}" required>
</div>
<div class="field">
    <span>Nama member</span>
    <input type="text" name="name" value="{{ old('name', $member->name ?? '') }}" required>
</div>
<div class="field">
    <span>Gender</span>
    <select name="gender">
        <option value="">Pilih</option>
        <option value="male" @selected(old('gender', $member->gender ?? '') === 'male')>Laki-laki</option>
        <option value="female" @selected(old('gender', $member->gender ?? '') === 'female')>Perempuan</option>
    </select>
</div>
<div class="field">
    <span>Status</span>
    <select name="status" required>
        <option value="active" @selected(old('status', $member->status ?? 'active') === 'active')>Aktif</option>
        <option value="inactive" @selected(old('status', $member->status ?? '') === 'inactive')>Nonaktif</option>
    </select>
</div>
<div class="field">
    <span>Target role</span>
    <input type="text" name="target_role" value="{{ old('target_role', $member->target_role ?? '') }}" placeholder="Calon bendahara">
</div>
<div class="field">
    <span>Target fungsi</span>
    <input type="text" name="target_function" value="{{ old('target_function', $member->target_function ?? '') }}" placeholder="Keuangan">
</div>
<div class="field">
    <span>Prioritas monitoring</span>
    <select name="note_priority" required>
        <option value="normal" @selected(old('note_priority', $member->note_priority ?? 'normal') === 'normal')>Normal</option>
        <option value="high" @selected(old('note_priority', $member->note_priority ?? '') === 'high')>High</option>
        <option value="urgent" @selected(old('note_priority', $member->note_priority ?? '') === 'urgent')>Urgent</option>
    </select>
</div>
