<?php

namespace App\Http\Controllers;

use App\Models\DevelopmentTarget;
use App\Models\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DevelopmentTargetController extends Controller
{
    protected function validatedData(Request $request): array
    {
        return $request->validate([
            'member_id' => ['required', 'exists:members,id'],
            'function_name' => ['required', 'string', 'max:255'],
            'role_name' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:20'],
            'priority' => ['required', 'string', 'max:20'],
            'goal' => ['nullable', 'string'],
            'next_action' => ['nullable', 'string'],
        ]);
    }

    public function index(): View
    {
        $targets = DevelopmentTarget::query()
            ->with(['member', 'assigner'])
            ->latest()
            ->paginate(12);

        return view('targets.index', compact('targets'));
    }

    public function create(): View
    {
        return view('targets.create', $this->formData());
    }

    public function edit(DevelopmentTarget $target): View
    {
        return view('targets.edit', [
            ...$this->formData(),
            'target' => $target,
        ]);
    }

    public function update(Request $request, DevelopmentTarget $target): RedirectResponse
    {
        $target->update($this->validatedData($request));

        return redirect()->route('targets.index')->with('status', 'Target pembinaan berhasil diperbarui.');
    }

    public function destroy(DevelopmentTarget $target): RedirectResponse
    {
        $target->delete();

        return redirect()->route('targets.index')->with('status', 'Target pembinaan berhasil dihapus.');
    }

    protected function formData(): array
    {
        return [
            'members' => Member::query()->orderBy('name')->get(),
        ];
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        DevelopmentTarget::create([
            ...$data,
            'assigned_by' => Auth::id(),
        ]);

        return redirect()->route('targets.index')->with('status', 'Target pembinaan berhasil ditambahkan.');
    }
}
