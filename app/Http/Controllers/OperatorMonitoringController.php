<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\CashTransaction;
use App\Models\ContributionPayment;
use App\Models\MemberNote;
use App\Models\ProgressUpdate;
use App\Models\User;
use Illuminate\View\View;

class OperatorMonitoringController extends Controller
{
    public function __invoke(): View
    {
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();
        $monthStartDate = $monthStart->toDateString();
        $monthEndDate = $monthEnd->toDateString();

        $operators = User::query()
            ->withCount([
                'createdActivities as activities_this_month_count' => fn ($query) => $query
                    ->whereBetween('scheduled_at', [$monthStart, $monthEnd]),
                'memberNotes as notes_this_month_count' => fn ($query) => $query
                    ->whereBetween('created_at', [$monthStart, $monthEnd]),
                'progressRecords as progress_this_month_count' => fn ($query) => $query
                    ->whereBetween('created_at', [$monthStart, $monthEnd]),
                'recordedContributionPayments as payments_this_month_count' => fn ($query) => $query
                    ->whereBetween('paid_on', [$monthStartDate, $monthEndDate]),
                'recordedCashTransactions as cash_transactions_this_month_count' => fn ($query) => $query
                    ->whereBetween('transaction_date', [$monthStartDate, $monthEndDate]),
            ])
            ->orderBy('role')
            ->orderBy('name')
            ->get()
            ->map(function (User $operator) {
                $latestActivity = Activity::query()
                    ->where('created_by', $operator->id)
                    ->latest('created_at')
                    ->first();

                $latestNote = MemberNote::query()
                    ->where('author_id', $operator->id)
                    ->latest()
                    ->first();

                $latestProgress = ProgressUpdate::query()
                    ->where('recorded_by', $operator->id)
                    ->latest()
                    ->first();

                $latestPayment = ContributionPayment::query()
                    ->where('recorded_by', $operator->id)
                    ->latest('paid_on')
                    ->first();

                $latestCashTransaction = CashTransaction::query()
                    ->where('recorded_by', $operator->id)
                    ->latest('transaction_date')
                    ->first();

                $latestTimestamps = collect([
                    $latestActivity?->created_at,
                    $latestNote?->created_at,
                    $latestProgress?->created_at,
                    $latestPayment?->created_at,
                    $latestCashTransaction?->created_at,
                ])->filter();

                $operator->last_operator_activity_at = $latestTimestamps->max();
                $operator->last_operator_activity_label = $this->resolveLastActivityLabel(
                    $operator,
                    $latestActivity,
                    $latestNote,
                    $latestProgress,
                    $latestPayment,
                    $latestCashTransaction
                );

                return $operator;
            });

        return view('operators.index', [
            'operators' => $operators,
            'activeOperators' => $operators->filter(fn ($operator) => $operator->last_operator_activity_at)->count(),
            'inactiveOperators' => $operators->filter(fn ($operator) => ! $operator->last_operator_activity_at)->count(),
        ]);
    }

    protected function resolveLastActivityLabel(
        User $operator,
        ?Activity $activity,
        ?MemberNote $note,
        ?ProgressUpdate $progress,
        ?ContributionPayment $payment,
        ?CashTransaction $cashTransaction
    ): string {
        $items = collect([
            ['label' => 'Input kegiatan: '.$activity?->title, 'time' => $activity?->created_at],
            ['label' => 'Tulis note: '.$note?->tag, 'time' => $note?->created_at],
            ['label' => 'Catat progress: '.$progress?->area, 'time' => $progress?->created_at],
            ['label' => 'Input pembayaran iuran', 'time' => $payment?->created_at],
            ['label' => 'Catat transaksi kas', 'time' => $cashTransaction?->created_at],
        ])->filter(fn ($item) => $item['time']);

        if ($items->isEmpty()) {
            return $operator->is_active ? 'Belum ada aktivitas tercatat' : 'Akun nonaktif';
        }

        return $items->sortByDesc('time')->first()['label'];
    }
}
