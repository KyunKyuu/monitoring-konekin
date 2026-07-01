<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\DevelopmentTarget;
use App\Models\Member;
use App\Models\ProgressUpdate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProgressUpdateController extends Controller
{
    protected function validatedData(Request $request): array
    {
        return $request->validate([
            'member_id' => ['required', 'exists:members,id'],
            'development_target_id' => ['nullable', 'exists:development_targets,id'],
            'activity_id' => ['nullable', 'exists:activities,id'],
            'area' => ['required', 'string', 'max:255'],
            'stage' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:30'],
            'summary' => ['required', 'string'],
        ]);
    }

    public function index(): View
    {
        $updates = ProgressUpdate::query()
            ->with(['member', 'target', 'activity', 'recorder'])
            ->latest()
            ->paginate(12);

        return view('progress.index', compact('updates'));
    }

    public function create(): View
    {
        return view('progress.create', $this->formData());
    }

    public function edit(ProgressUpdate $progress): View
    {
        return view('progress.edit', [
            ...$this->formData(),
            'progress' => $progress,
        ]);
    }

    public function update(Request $request, ProgressUpdate $progress): RedirectResponse
    {
        $progress->update($this->validatedData($request));

        return redirect()->route('progress.index')->with('status', 'Progress berhasil diperbarui.');
    }

    public function destroy(ProgressUpdate $progress): RedirectResponse
    {
        $progress->delete();

        return redirect()->route('progress.index')->with('status', 'Progress berhasil dihapus.');
    }

    protected function formData(): array
    {
        return [
            'members' => Member::query()->orderBy('name')->get(),
            'targets' => DevelopmentTarget::query()->with('member')->latest()->get(),
            'activities' => Activity::query()->orderByDesc('scheduled_at')->get(),
        ];
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        ProgressUpdate::create([
            ...$data,
            'recorded_by' => Auth::id(),
        ]);

        return redirect()->route('progress.index')->with('status', 'Progress berhasil dicatat.');
    }
}
