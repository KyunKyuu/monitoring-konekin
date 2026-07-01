<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Member;
use App\Models\MemberNote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MemberNoteController extends Controller
{
    protected function validatedData(Request $request): array
    {
        return $request->validate([
            'member_id' => ['required', 'exists:members,id'],
            'activity_id' => ['nullable', 'exists:activities,id'],
            'tag' => ['required', 'string', 'max:100'],
            'level' => ['required', 'string', 'max:20'],
            'follow_up_status' => ['required', 'string', 'max:20'],
            'content' => ['required', 'string'],
            'follow_up_action' => ['nullable', 'string'],
        ]);
    }

    public function index(Request $request): View
    {
        $tag = $request->string('tag')->toString();

        $notes = MemberNote::query()
            ->with(['member', 'author', 'activity'])
            ->when($tag, fn ($query) => $query->where('tag', $tag))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $tags = MemberNote::query()->distinct()->orderBy('tag')->pluck('tag');

        return view('notes.index', compact('notes', 'tags', 'tag'));
    }

    public function create(): View
    {
        return view('notes.create', $this->formData());
    }

    public function edit(MemberNote $note): View
    {
        return view('notes.edit', [
            ...$this->formData(),
            'note' => $note,
        ]);
    }

    public function update(Request $request, MemberNote $note): RedirectResponse
    {
        $note->update($this->validatedData($request));

        return redirect()->route('notes.index')->with('status', 'Note berhasil diperbarui.');
    }

    public function destroy(MemberNote $note): RedirectResponse
    {
        $note->delete();

        return redirect()->route('notes.index')->with('status', 'Note berhasil dihapus.');
    }

    protected function formData(): array
    {
        return [
            'members' => Member::query()->orderBy('name')->get(),
            'activities' => Activity::query()->orderByDesc('scheduled_at')->get(),
        ];
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        MemberNote::create([
            ...$data,
            'author_id' => Auth::id(),
        ]);

        return redirect()->route('notes.index')->with('status', 'Note berhasil ditambahkan.');
    }
}
