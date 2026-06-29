<?php

namespace App\Http\Controllers;

use App\Models\IdealPosition;
use App\Models\Member;
use App\Models\PositionCandidate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PositionCandidateController extends Controller
{
    protected function validatedData(Request $request): array
    {
        return $request->validate([
            'ideal_position_id' => ['required', 'exists:ideal_positions,id'],
            'member_id' => ['required', 'exists:members,id'],
            'status' => ['required', 'string', 'max:20'],
            'notes' => ['nullable', 'string'],
        ]);
    }

    public function index(): View
    {
        $candidates = PositionCandidate::query()
            ->with(['idealPosition', 'member', 'assigner'])
            ->latest()
            ->paginate(12);

        return view('position-candidates.index', compact('candidates'));
    }

    public function create(): View
    {
        return view('position-candidates.create', $this->formData());
    }

    public function edit(PositionCandidate $position_candidate): View
    {
        return view('position-candidates.edit', [
            ...$this->formData(),
            'positionCandidate' => $position_candidate,
        ]);
    }

    public function update(Request $request, PositionCandidate $position_candidate): RedirectResponse
    {
        $position_candidate->update($this->validatedData($request));

        return redirect()->route('position-candidates.index')->with('status', 'Kandidat jabatan berhasil diperbarui.');
    }

    public function destroy(PositionCandidate $position_candidate): RedirectResponse
    {
        $position_candidate->delete();

        return redirect()->route('position-candidates.index')->with('status', 'Kandidat jabatan berhasil dihapus.');
    }

    protected function formData(): array
    {
        return [
            'positions' => IdealPosition::query()->orderBy('function_name')->get(),
            'members' => Member::query()->orderBy('name')->get(),
        ];
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        PositionCandidate::create([
            ...$data,
            'assigned_by' => Auth::id(),
        ]);

        return redirect()->route('position-candidates.index')->with('status', 'Kandidat jabatan berhasil ditambahkan.');
    }
}
