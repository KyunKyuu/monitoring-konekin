<?php

namespace App\Http\Controllers;

use App\Models\Contribution;
use App\Models\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContributionController extends Controller
{
    protected function validatedData(Request $request): array
    {
        return $request->validate([
            'member_id' => ['required', 'exists:members,id'],
            'period_month' => ['required', 'integer', 'between:1,12'],
            'period_year' => ['required', 'integer', 'min:2024'],
            'amount_due' => ['required', 'numeric', 'min:0'],
            'due_date' => ['nullable', 'date'],
        ]);
    }

    public function index(): View
    {
        $contributions = Contribution::query()
            ->with(['member', 'payments'])
            ->latest()
            ->paginate(12);

        return view('finance.contributions.index', compact('contributions'));
    }

    public function create(): View
    {
        return view('finance.contributions.create', $this->formData());
    }

    public function edit(Contribution $contribution): View
    {
        return view('finance.contributions.edit', [
            ...$this->formData(),
            'contribution' => $contribution,
        ]);
    }

    public function update(Request $request, Contribution $contribution): RedirectResponse
    {
        $data = $this->validatedData($request);

        $contribution->update([
            ...$data,
            'status' => $this->resolveStatus((float) $contribution->amount_paid, (float) $data['amount_due']),
        ]);

        return redirect()->route('contributions.index')->with('status', 'Tagihan iuran berhasil diperbarui.');
    }

    public function destroy(Contribution $contribution): RedirectResponse
    {
        $contribution->payments()->delete();
        $contribution->delete();

        return redirect()->route('contributions.index')->with('status', 'Tagihan iuran berhasil dihapus.');
    }

    protected function formData(): array
    {
        return [
            'members' => Member::query()->orderBy('name')->get(),
        ];
    }

    protected function resolveStatus(float $amountPaid, float $amountDue): string
    {
        if ($amountPaid <= 0) {
            return 'unpaid';
        }

        return $amountPaid < $amountDue ? 'partial' : 'paid';
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        Contribution::create([
            ...$data,
            'amount_paid' => 0,
            'status' => 'unpaid',
        ]);

        return redirect()->route('contributions.index')->with('status', 'Tagihan iuran berhasil dibuat.');
    }
}
