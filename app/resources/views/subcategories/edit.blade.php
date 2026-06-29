@extends('layouts.dashboard', ['title' => 'Edit Sub Kategori'])

@section('dashboard-content')
    <section class="page-header">
        <div>
            <div class="mono-eyebrow">MASTER AKTIVITAS</div>
            <h1 class="page-title">Edit sub kategori</h1>
        </div>
    </section>

    <section class="feature-card">
        <form method="POST" action="{{ route('subcategories.update', $subcategory) }}" class="form-grid">
            @csrf
            @method('PUT')
            <div class="field"><span>Kategori induk</span>
                <select name="category_id" required>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id', $subcategory->category_id) == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field"><span>Nama sub kategori</span><input type="text" name="name" value="{{ old('name', $subcategory->name) }}" required></div>
            <div class="field field-full"><span>Deskripsi</span><textarea name="description" rows="4">{{ old('description', $subcategory->description) }}</textarea></div>
            <button type="submit" class="button button-primary">Update sub kategori</button>
        </form>
    </section>
@endsection
