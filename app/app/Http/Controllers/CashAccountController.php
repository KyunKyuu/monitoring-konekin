<?php

namespace App\Http\Controllers;

use App\Models\CashAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CashAccountController extends Controller
{
    protected function validatedData(Request $request, ?CashAccount $cashAccount = null): array
    {
        return $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('cash_accounts', 'code')->ignore($cashAccount)],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }

    public function index(): View
    {
        $accounts = CashAccount::query()->withCount('transactions')->latest()->get();

        return view('finance.cash-accounts.index', compact('accounts'));
    }

    public function create(): View
    {
        return view('finance.cash-accounts.create');
    }

    public function edit(CashAccount $cash_account): View
    {
        return view('finance.cash-accounts.edit', ['cashAccount' => $cash_account]);
    }

    public function update(Request $request, CashAccount $cash_account): RedirectResponse
    {
        $data = $this->validatedData($request, $cash_account);

        $cash_account->update([
            ...$data,
            'is_active' => (bool) ($data['is_active'] ?? false),
        ]);

        return redirect()->route('cash-accounts.index')->with('status', 'Akun kas berhasil diperbarui.');
    }

    public function destroy(CashAccount $cash_account): RedirectResponse
    {
        $cash_account->transactions()->delete();
        $cash_account->delete();

        return redirect()->route('cash-accounts.index')->with('status', 'Akun kas berhasil dihapus.');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        CashAccount::create([
            ...$data,
            'is_active' => (bool) ($data['is_active'] ?? false),
        ]);

        return redirect()->route('cash-accounts.index')->with('status', 'Akun kas berhasil dibuat.');
    }
}
