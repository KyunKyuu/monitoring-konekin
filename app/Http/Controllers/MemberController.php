<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MemberController extends Controller
{
    protected function formData(?Member $member = null): array
    {
        return [
            'kakaTingkatOptions' => Member::query()
                ->when($member, fn ($query) => $query->where('id', '!=', $member->id))
                ->orderBy('name')
                ->get(),
        ];
    }

    protected function validatedData(Request $request, ?Member $member = null): array
    {
        return $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:members,code,'.($member?->id ?? 'null')],
            'name' => ['required', 'string', 'max:255'],
            'kaka_tingkat_id' => ['nullable', 'exists:members,id'],
            'gender' => ['nullable', 'string', 'max:20'],
            'status' => ['required', 'string', 'max:20'],
            'target_role' => ['nullable', 'string', 'max:255'],
            'target_function' => ['nullable', 'string', 'max:255'],
            'note_priority' => ['required', 'string', 'max:20'],
        ]);
    }

    public function index(Request $request): View
    {
        $search = $request->string('q')->toString();

        $members = Member::query()
            ->when($search, fn ($query) => $query
                ->where('name', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%")
                ->orWhere('target_role', 'like', "%{$search}%"))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('members.index', compact('members', 'search'));
    }

    public function create(): View
    {
        return view('members.create', $this->formData());
    }

    public function edit(Member $member): View
    {
        return view('members.edit', [
            'member' => $member,
            ...$this->formData($member),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        Member::create($data);

        return redirect()->route('members.index')->with('status', 'Member berhasil ditambahkan.');
    }

    public function update(Request $request, Member $member): RedirectResponse
    {
        $data = $this->validatedData($request, $member);

        $member->update($data);

        return redirect()->route('members.show', $member)->with('status', 'Member berhasil diperbarui.');
    }

    public function destroy(Member $member): RedirectResponse
    {
        $member->delete();

        return redirect()->route('members.index')->with('status', 'Member berhasil dihapus.');
    }

    public function show(Member $member): View
    {
        $member->load([
            'kakaTingkat',
            'adikTingkat',
            'activities',
            'notes.author',
            'notes.activity',
            'developmentTargets.assigner',
            'progressUpdates.target',
            'progressUpdates.activity',
            'contributions.payments',
        ]);

        return view('members.show', compact('member'));
    }

    public function hierarchy(): View
    {
        $leaders = Member::query()
            ->with(['adikTingkat.adikTingkat', 'kakaTingkat'])
            ->whereHas('adikTingkat')
            ->orderBy('name')
            ->get();

        $independentMembers = Member::query()
            ->with('kakaTingkat')
            ->whereNull('kaka_tingkat_id')
            ->whereDoesntHave('adikTingkat')
            ->orderBy('name')
            ->get();

        $allMembers = Member::query()->orderBy('name')->get();

        return view('members.hierarchy', compact('leaders', 'independentMembers', 'allMembers'));
    }

    public function updateHierarchy(Request $request, Member $member): RedirectResponse
    {
        $data = $request->validate([
            'kaka_tingkat_id' => ['nullable', 'exists:members,id'],
        ]);

        $kakaTingkatId = $data['kaka_tingkat_id'] ?? null;

        if ($kakaTingkatId && (int) $kakaTingkatId === $member->id) {
            return back()->withErrors(['kaka_tingkat_id' => 'Kaka tingkat tidak boleh menunjuk diri sendiri.']);
        }

        $descendantIds = $member->adikTingkat()->pluck('id');
        if ($kakaTingkatId && $descendantIds->contains((int) $kakaTingkatId)) {
            return back()->withErrors(['kaka_tingkat_id' => 'Kaka tingkat tidak boleh diisi dengan adik langsungnya sendiri.']);
        }

        $member->update([
            'kaka_tingkat_id' => $kakaTingkatId,
        ]);

        return redirect()->route('members.hierarchy')->with('status', 'Struktur kaka tingkat berhasil diperbarui.');
    }

    public function downloadTemplate(): Response
    {
        $csv = implode("\n", [
            'code,name,kaka_tingkat_code,gender,status,target_role,target_function,note_priority',
            'MBR-001,Bintang A.,,male,active,Calon Bendahara,Keuangan,medium',
            'MBR-002,Mira K.,MBR-001,female,active,,,low',
        ]);

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="member-import-template.csv"',
        ]);
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $rows = array_map('str_getcsv', file($request->file('file')->getRealPath()));

        if (count($rows) < 2) {
            return back()->withErrors(['file' => 'File CSV kosong atau tidak punya data.']);
        }

        $header = array_map(fn ($value) => strtolower(trim((string) $value)), array_shift($rows));
        $required = ['code', 'name'];

        foreach ($required as $column) {
            if (! in_array($column, $header, true)) {
                return back()->withErrors(['file' => "Kolom wajib `{$column}` tidak ditemukan di CSV."]);
            }
        }

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        DB::transaction(function () use ($rows, $header, &$created, &$updated, &$skipped, &$errors) {
            $pendingKakaTingkat = [];

            foreach ($rows as $index => $row) {
                if (count(array_filter($row, fn ($value) => trim((string) $value) !== '')) === 0) {
                    continue;
                }

                $payload = [];
                foreach ($header as $columnIndex => $columnName) {
                    $payload[$columnName] = isset($row[$columnIndex]) ? trim((string) $row[$columnIndex]) : null;
                }

                $code = $payload['code'] ?? null;
                $name = $payload['name'] ?? null;

                if (! $code || ! $name) {
                    $errors[] = 'Baris '.($index + 2).' dilewati karena `code` atau `name` kosong.';
                    $skipped++;
                    continue;
                }

                $memberData = [
                    'name' => $name,
                    'gender' => $payload['gender'] ?: null,
                    'status' => $payload['status'] ?: 'active',
                    'target_role' => $payload['target_role'] ?: null,
                    'target_function' => $payload['target_function'] ?: null,
                    'note_priority' => $payload['note_priority'] ?: 'medium',
                ];

                $member = Member::query()->where('code', $code)->first();

                if ($member) {
                    $member->update($memberData);
                    $pendingKakaTingkat[$member->code] = $payload['kaka_tingkat_code'] ?: null;
                    $updated++;
                    continue;
                }

                $createdMember = Member::create([
                    'code' => $code,
                    ...$memberData,
                ]);
                $pendingKakaTingkat[$createdMember->code] = $payload['kaka_tingkat_code'] ?: null;
                $created++;
            }

            foreach ($pendingKakaTingkat as $memberCode => $kakaTingkatCode) {
                if (! $kakaTingkatCode) {
                    continue;
                }

                $member = Member::query()->where('code', $memberCode)->first();
                $kakaTingkat = Member::query()->where('code', $kakaTingkatCode)->first();

                if (! $member || ! $kakaTingkat) {
                    $errors[] = 'Relasi kaka tingkat untuk `'.$memberCode.'` dilewati karena kode `'.$kakaTingkatCode.'` tidak ditemukan.';
                    continue;
                }

                if ($member->id === $kakaTingkat->id) {
                    $errors[] = 'Relasi kaka tingkat untuk `'.$memberCode.'` dilewati karena tidak boleh menunjuk diri sendiri.';
                    continue;
                }

                $member->update(['kaka_tingkat_id' => $kakaTingkat->id]);
            }
        });

        $message = "Import selesai. {$created} dibuat, {$updated} diperbarui, {$skipped} dilewati.";

        if ($errors !== []) {
            $message .= ' '.implode(' ', array_slice($errors, 0, 3));
        }

        return redirect()->route('members.index')->with('status', $message);
    }
}
