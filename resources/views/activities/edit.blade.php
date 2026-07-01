@extends('layouts.dashboard', ['title' => 'Edit Kegiatan'])

@section('dashboard-content')
    <section class="page-header">
        <div>
            <div class="mono-eyebrow">KEGIATAN</div>
            <h1 class="page-title">Edit agenda</h1>
        </div>
    </section>

    <section class="feature-card">
        <form method="POST" action="{{ route('activities.update', $activity) }}" class="form-grid">
            @csrf
            @method('PUT')
            <div class="field"><span>Kategori</span>
                <select name="category_id" required>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id', $activity->category_id) == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field"><span>Sub kategori</span>
                <select name="subcategory_id">
                    <option value="">Tanpa sub kategori</option>
                    @foreach ($categories as $category)
                        @foreach ($category->subcategories as $subcategory)
                            <option value="{{ $subcategory->id }}" @selected(old('subcategory_id', $activity->subcategory_id) == $subcategory->id)>{{ $category->name }} · {{ $subcategory->name }}</option>
                        @endforeach
                    @endforeach
                </select>
            </div>
            <div class="field"><span>Judul kegiatan</span><input type="text" name="title" value="{{ old('title', $activity->title) }}" required></div>
            <div class="field"><span>Tema</span><input type="text" name="theme" value="{{ old('theme', $activity->theme) }}"></div>
            <div class="field"><span>Jadwal</span><input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at', $activity->scheduled_at->format('Y-m-d\\TH:i')) }}" required></div>
            <div class="field"><span>Lokasi</span><input type="text" name="location" value="{{ old('location', $activity->location) }}"></div>
            <div class="field"><span>Status</span>
                <select name="status">
                    @foreach (['scheduled', 'ongoing', 'completed'] as $status)
                        <option value="{{ $status }}" @selected(old('status', $activity->status) === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field field-full"><span>Note ringkas</span><textarea name="summary_note" rows="4">{{ old('summary_note', $activity->summary_note) }}</textarea></div>
            <div class="field field-full">
                <span>Pilih member yang ikut</span>
                <div class="checkbox-grid">
                    @php
                        $selectedMembers = old('member_ids', $activity->members->pluck('id')->all());
                    @endphp
                    @foreach ($members as $member)
                        <label class="checkbox-card">
                            <input type="checkbox" name="member_ids[]" value="{{ $member->id }}" @checked(in_array($member->id, $selectedMembers))>
                            <span>{{ $member->name }}</span>
                            <small>{{ $member->code }}</small>
                        </label>
                    @endforeach
                </div>
            </div>
            <button type="submit" class="button button-primary">Update kegiatan</button>
        </form>
    </section>
@endsection
