<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\CashAccount;
use App\Models\CashTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CashTransactionController extends Controller
{
    protected function validatedData(Request $request): array
    {
        return $request->validate([
            'cash_account_id' => ['required', 'exists:cash_accounts,id'],
            'activity_id' => ['nullable', 'exists:activities,id'],
            'type' => ['required', 'string', 'max:20'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'transaction_date' => ['required', 'date'],
            'category' => ['nullable', 'string', 'max:100'],
            'description' => ['required', 'string'],
        ]);
    }

    public function index(): View
    {
        $transactions = CashTransaction::query()
            ->with(['cashAccount', 'activity', 'recorder'])
            ->latest('transaction_date')
            ->paginate(12);

        return view('finance.transactions.index', compact('transactions'));
    }

    public function create(): View
    {
        return view('finance.transactions.create', $this->formData());
    }

    public function edit(CashTransaction $cash_transaction): View
    {
        return view('finance.transactions.edit', [
            ...$this->formData(),
            'cashTransaction' => $cash_transaction,
        ]);
    }

    public function update(Request $request, CashTransaction $cash_transaction): RedirectResponse
    {
        $cash_transaction->update($this->validatedData($request));

        return redirect()->route('cash-transactions.index')->with('status', 'Transaksi kas berhasil diperbarui.');
    }

    public function destroy(CashTransaction $cash_transaction): RedirectResponse
    {
        $cash_transaction->delete();

        return redirect()->route('cash-transactions.index')->with('status', 'Transaksi kas berhasil dihapus.');
    }

    protected function formData(): array
    {
        return [
            'cashAccounts' => CashAccount::query()->orderBy('name')->get(),
            'activities' => Activity::query()->orderByDesc('scheduled_at')->get(),
        ];
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        CashTransaction::create([
            ...$data,
            'recorded_by' => Auth::id(),
        ]);

        return redirect()->route('cash-transactions.index')->with('status', 'Transaksi kas berhasil dicatat.');
    }
}
