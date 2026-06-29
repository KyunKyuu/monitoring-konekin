@extends('layouts.dashboard', ['title' => 'Buat Kegiatan'])

@section('dashboard-content')
    <section class="page-header">
        <div>
            <div class="mono-eyebrow">KEGIATAN</div>
            <h1 class="page-title">Buat agenda baru</h1>
        </div>
    </section>

    <section class="feature-card">
        <form method="POST" action="{{ route('activities.store') }}" class="form-grid">
            @csrf
            <div class="field"><span>Kategori</span>
                <select name="category_id" required>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((int) old('category_id') === $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field"><span>Sub kategori</span>
                <select name="subcategory_id">
                    <option value="">Tanpa sub kategori</option>
                    @foreach ($categories as $category)
                        @foreach ($category->subcategories as $subcategory)
                            <option value="{{ $subcategory->id }}" @selected((int) old('subcategory_id') === $subcategory->id)>{{ $category->name }} · {{ $subcategory->name }}</option>
                        @endforeach
                    @endforeach
                </select>
            </div>
            <div class="field"><span>Judul kegiatan</span><input type="text" name="title" value="{{ old('title') }}" required></div>
            <div class="field"><span>Tema</span><input type="text" name="theme" value="{{ old('theme') }}"></div>
            <div class="field"><span>Jadwal</span><input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}" required></div>
            <div class="field"><span>Lokasi</span><input type="text" name="location" value="{{ old('location') }}"></div>
            <div class="field"><span>Status</span>
                <select name="status">
                    <option value="scheduled">Scheduled</option>
                    <option value="ongoing">Ongoing</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
            <div class="field field-full">
                <p class="empty-state">Master kategori bisa dikelola oleh super admin agar input kegiatan tetap konsisten.</p>
            </div>
            <div class="field field-full"><span>Note ringkas</span><textarea name="summary_note" rows="4">{{ old('summary_note') }}</textarea></div>
            <div class="field field-full">
                <span>Pilih member yang ikut</span>
                <div class="checkbox-grid">
                    @foreach ($members as $member)
                        <label class="checkbox-card">
                            <input type="checkbox" name="member_ids[]" value="{{ $member->id }}">
                            <span>{{ $member->name }}</span>
                            <small>{{ $member->code }}</small>
                        </label>
                    @endforeach
                </div>
            </div>
            <button type="submit" class="button button-primary">Simpan kegiatan</button>
        </form>
    </section>
@endsection
