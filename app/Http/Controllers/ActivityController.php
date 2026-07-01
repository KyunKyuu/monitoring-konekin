<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Category;
use App\Models\Member;
use App\Models\Subcategory;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ActivityController extends Controller
{
    protected function validatedData(Request $request): array
    {
        return $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'subcategory_id' => ['nullable', 'exists:subcategories,id'],
            'title' => ['required', 'string', 'max:255'],
            'theme' => ['nullable', 'string', 'max:255'],
            'scheduled_at' => ['required', 'date'],
            'location' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:30'],
            'summary_note' => ['nullable', 'string'],
            'member_ids' => ['array'],
            'member_ids.*' => ['integer', 'exists:members,id'],
        ]);
    }

    public function index(): View
    {
        $activities = Activity::query()
            ->with(['creator', 'members', 'categoryModel', 'subcategoryModel'])
            ->orderBy('scheduled_at')
            ->paginate(10);

        return view('activities.index', compact('activities'));
    }

    public function create(): View
    {
        $members = Member::query()->orderBy('name')->get();
        $categories = Category::query()->with('subcategories')->where('is_active', true)->orderBy('name')->get();

        return view('activities.create', compact('members', 'categories'));
    }

    public function edit(Activity $activity): View
    {
        $members = Member::query()->orderBy('name')->get();
        $categories = Category::query()->with('subcategories')->where('is_active', true)->orderBy('name')->get();
        $activity->load('members');

        return view('activities.edit', compact('activity', 'members', 'categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        $category = Category::findOrFail((int) $data['category_id']);
        $subcategory = ! empty($data['subcategory_id']) ? Subcategory::find((int) $data['subcategory_id']) : null;

        $activity = Activity::create([
            ...collect($data)->except('member_ids')->all(),
            'created_by' => Auth::id(),
            'category' => $category->name,
            'sub_category' => $subcategory?->name,
        ]);

        $memberIds = collect($data['member_ids'] ?? [])->mapWithKeys(fn ($id) => [$id => [
            'role_in_activity' => 'participant',
            'attendance_status' => 'planned',
        ]]);

        $activity->members()->sync($memberIds);

        return redirect()->route('activities.show', $activity)->with('status', 'Kegiatan berhasil dibuat.');
    }

    public function update(Request $request, Activity $activity): RedirectResponse
    {
        $data = $this->validatedData($request);

        $category = Category::findOrFail((int) $data['category_id']);
        $subcategory = ! empty($data['subcategory_id']) ? Subcategory::find((int) $data['subcategory_id']) : null;

        $activity->update([
            ...collect($data)->except('member_ids')->all(),
            'category' => $category->name,
            'sub_category' => $subcategory?->name,
        ]);

        $memberIds = collect($data['member_ids'] ?? [])->mapWithKeys(fn ($id) => [$id => [
            'role_in_activity' => 'participant',
            'attendance_status' => 'planned',
        ]]);

        $activity->members()->sync($memberIds);

        return redirect()->route('activities.show', $activity)->with('status', 'Kegiatan berhasil diperbarui.');
    }

    public function destroy(Activity $activity): RedirectResponse
    {
        $activity->delete();

        return redirect()->route('activities.index')->with('status', 'Kegiatan berhasil dihapus.');
    }

    public function show(Activity $activity): View
    {
        $activity->load(['creator', 'members', 'notes.member', 'notes.author', 'categoryModel', 'subcategoryModel']);

        return view('activities.show', compact('activity'));
    }

    public function calendar(Request $request): View
    {
        $baseDate = Carbon::createFromDate(
            (int) $request->integer('year', now()->year),
            (int) $request->integer('month', now()->month),
            1
        )->startOfMonth();

        $start = $baseDate->copy()->startOfWeek(Carbon::SUNDAY);
        $end = $baseDate->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);

        $activities = Activity::query()
            ->with(['members', 'categoryModel'])
            ->whereBetween('scheduled_at', [$start->copy()->startOfDay(), $end->copy()->endOfDay()])
            ->orderBy('scheduled_at')
            ->get()
            ->groupBy(fn (Activity $activity) => $activity->scheduled_at->format('Y-m-d'));

        $weeks = [];
        $cursor = $start->copy();

        while ($cursor->lte($end)) {
            $week = [];

            for ($day = 0; $day < 7; $day++) {
                $dateKey = $cursor->format('Y-m-d');

                $week[] = [
                    'date' => $cursor->copy(),
                    'isCurrentMonth' => $cursor->month === $baseDate->month,
                    'isToday' => $cursor->isToday(),
                    'activities' => $activities->get($dateKey, collect()),
                ];

                $cursor->addDay();
            }

            $weeks[] = $week;
        }

        return view('activities.calendar', [
            'weeks' => $weeks,
            'month' => $baseDate,
            'previousMonth' => $baseDate->copy()->subMonth(),
            'nextMonth' => $baseDate->copy()->addMonth(),
        ]);
    }

    public function downloadTemplate(): Response
    {
        $csv = implode("\n", [
            'title,category_name,subcategory_name,theme,scheduled_at,location,status,summary_note,member_codes',
            '"Kelas Malam Sholat",Kelas,"Materi Sholat","Materi sholat dasar",2026-06-28 22:00:00,"Basecamp",planned,"Kelas malam batch 1","MBR-001|MBR-002|MBR-003"',
            '"Agenda Fisik Pagi",Olahraga,"Agenda Fisik Pagi","Latihan stamina",2026-06-29 05:30:00,"Lapangan",planned,,"MBR-002|MBR-004"',
        ]);

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="activity-import-template.csv"',
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
        $required = ['title', 'category_name', 'scheduled_at', 'status'];

        foreach ($required as $column) {
            if (! in_array($column, $header, true)) {
                return back()->withErrors(['file' => "Kolom wajib `{$column}` tidak ditemukan di CSV."]);
            }
        }

        $created = 0;
        $skipped = 0;
        $errors = [];

        DB::transaction(function () use ($rows, $header, &$created, &$skipped, &$errors) {
            foreach ($rows as $index => $row) {
                if (count(array_filter($row, fn ($value) => trim((string) $value) !== '')) === 0) {
                    continue;
                }

                $payload = [];
                foreach ($header as $columnIndex => $columnName) {
                    $payload[$columnName] = isset($row[$columnIndex]) ? trim((string) $row[$columnIndex]) : null;
                }

                $title = $payload['title'] ?? null;
                $categoryName = $payload['category_name'] ?? null;
                $scheduledAt = $payload['scheduled_at'] ?? null;
                $status = $payload['status'] ?? null;

                if (! $title || ! $categoryName || ! $scheduledAt || ! $status) {
                    $errors[] = 'Baris '.($index + 2).' dilewati karena kolom wajib kosong.';
                    $skipped++;
                    continue;
                }

                $category = Category::query()->where('name', $categoryName)->first();
                if (! $category) {
                    $errors[] = 'Baris '.($index + 2).' dilewati karena kategori `'.$categoryName.'` tidak ditemukan.';
                    $skipped++;
                    continue;
                }

                $subcategory = null;
                if (! empty($payload['subcategory_name'])) {
                    $subcategory = Subcategory::query()
                        ->where('name', $payload['subcategory_name'])
                        ->where('category_id', $category->id)
                        ->first();

                    if (! $subcategory) {
                        $errors[] = 'Baris '.($index + 2).' dilewati karena subkategori `'.$payload['subcategory_name'].'` tidak valid.';
                        $skipped++;
                        continue;
                    }
                }

                try {
                    $scheduledDate = Carbon::parse($scheduledAt);
                } catch (\Throwable) {
                    $errors[] = 'Baris '.($index + 2).' dilewati karena format `scheduled_at` tidak valid.';
                    $skipped++;
                    continue;
                }

                $activity = Activity::create([
                    'created_by' => Auth::id(),
                    'category_id' => $category->id,
                    'subcategory_id' => $subcategory?->id,
                    'category' => $category->name,
                    'sub_category' => $subcategory?->name,
                    'title' => $title,
                    'theme' => $payload['theme'] ?: null,
                    'scheduled_at' => $scheduledDate,
                    'location' => $payload['location'] ?: null,
                    'status' => $status,
                    'summary_note' => $payload['summary_note'] ?: null,
                ]);

                $codes = collect(explode('|', (string) ($payload['member_codes'] ?? '')))
                    ->map(fn ($code) => trim($code))
                    ->filter();

                if ($codes->isNotEmpty()) {
                    $members = Member::query()->whereIn('code', $codes->all())->pluck('id', 'code');

                    $syncData = $members->mapWithKeys(fn ($id) => [$id => [
                        'role_in_activity' => 'participant',
                        'attendance_status' => 'planned',
                    ]]);

                    $activity->members()->sync($syncData);

                    $missingCodes = $codes->diff($members->keys());
                    if ($missingCodes->isNotEmpty()) {
                        $errors[] = 'Baris '.($index + 2).' ada kode member yang tidak ditemukan: '.$missingCodes->implode(', ').'.';
                    }
                }

                $created++;
            }
        });

        $message = "Import kegiatan selesai. {$created} dibuat, {$skipped} dilewati.";

        if ($errors !== []) {
            $message .= ' '.implode(' ', array_slice($errors, 0, 3));
        }

        return redirect()->route('activities.index')->with('status', $message);
    }
}
