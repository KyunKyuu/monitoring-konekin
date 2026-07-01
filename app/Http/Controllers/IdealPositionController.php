<?php

namespace App\Http\Controllers;

use App\Models\IdealPosition;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IdealPositionController extends Controller
{
    protected function validatedData(Request $request): array
    {
        return $request->validate([
            'function_name' => ['required', 'string', 'max:255'],
            'position_name' => ['required', 'string', 'max:255'],
            'goal' => ['nullable', 'string'],
            'responsibilities' => ['nullable', 'string'],
            'required_count' => ['required', 'integer', 'min:1'],
            'status' => ['required', 'string', 'max:20'],
        ]);
    }

    public function index(): View
    {
        $positions = IdealPosition::query()->withCount('candidates')->latest()->get();

        return view('ideal-positions.index', compact('positions'));
    }

    public function create(): View
    {
        return view('ideal-positions.create');
    }

    public function edit(IdealPosition $ideal_position): View
    {
        return view('ideal-positions.edit', ['idealPosition' => $ideal_position]);
    }

    public function update(Request $request, IdealPosition $ideal_position): RedirectResponse
    {
        $ideal_position->update($this->validatedData($request));

        return redirect()->route('ideal-positions.index')->with('status', 'Posisi ideal berhasil diperbarui.');
    }

    public function destroy(IdealPosition $ideal_position): RedirectResponse
    {
        $ideal_position->candidates()->delete();
        $ideal_position->delete();

        return redirect()->route('ideal-positions.index')->with('status', 'Posisi ideal berhasil dihapus.');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        IdealPosition::create($data);

        return redirect()->route('ideal-positions.index')->with('status', 'Posisi ideal berhasil dibuat.');
    }
}
