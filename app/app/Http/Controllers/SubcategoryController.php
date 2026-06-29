<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubcategoryController extends Controller
{
    public function index(): View
    {
        $subcategories = Subcategory::query()->with('category')->orderBy('name')->get();

        return view('subcategories.index', compact('subcategories'));
    }

    public function create(): View
    {
        return view('subcategories.create', [
            'categories' => Category::query()->orderBy('name')->get(),
        ]);
    }

    public function edit(Subcategory $subcategory): View
    {
        return view('subcategories.edit', [
            'subcategory' => $subcategory,
            'categories' => Category::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        Subcategory::create($data);

        return redirect()->route('subcategories.index')->with('status', 'Sub kategori berhasil dibuat.');
    }

    public function update(Request $request, Subcategory $subcategory): RedirectResponse
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $subcategory->update($data);

        return redirect()->route('subcategories.index')->with('status', 'Sub kategori berhasil diperbarui.');
    }

    public function destroy(Subcategory $subcategory): RedirectResponse
    {
        $subcategory->delete();

        return redirect()->route('subcategories.index')->with('status', 'Sub kategori berhasil dihapus.');
    }
}
