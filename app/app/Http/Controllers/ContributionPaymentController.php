<?php

namespace App\Http\Controllers;

use App\Models\CashAccount;
use App\Models\CashTransaction;
use App\Models\Contribution;
use App\Models\ContributionPayment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ContributionPaymentController extends Controller
{
    public function create(): View
    {
        return view('finance.payments.create', [
            'contributions' => Contribution::query()->with('member')->latest()->get(),
            'cashAccounts' => CashAccount::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'contribution_id' => ['required', 'exists:contributions,id'],
            'cash_account_id' => ['nullable', 'exists:cash_accounts,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'paid_on' => ['required', 'date'],
            'payment_method' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        $payment = ContributionPayment::create([
            'contribution_id' => $data['contribution_id'],
            'recorded_by' => Auth::id(),
            'amount' => $data['amount'],
            'paid_on' => $data['paid_on'],
            'payment_method' => $data['payment_method'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        $contribution = Contribution::findOrFail($data['contribution_id']);
        $contribution->amount_paid = $contribution->payments()->sum('amount');
        $contribution->status = $contribution->amount_paid <= 0
            ? 'unpaid'
            : ($contribution->amount_paid < $contribution->amount_due ? 'partial' : 'paid');
        $contribution->save();

        if (! empty($data['cash_account_id'])) {
            CashTransaction::create([
                'cash_account_id' => $data['cash_account_id'],
                'recorded_by' => Auth::id(),
                'type' => 'income',
                'amount' => $payment->amount,
                'transaction_date' => $payment->paid_on,
                'category' => 'Iuran',
                'description' => 'Pembayaran iuran '.$contribution->member->name.' periode '.$contribution->period_month.'/'.$contribution->period_year,
            ]);
        }

        return redirect()->route('contributions.index')->with('status', 'Pembayaran iuran berhasil dicatat.');
    }
}
