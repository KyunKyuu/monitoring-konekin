<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\CashTransaction;
use App\Models\Contribution;
use App\Models\DevelopmentTarget;
use App\Models\IdealPosition;
use App\Models\Member;
use App\Models\MemberNote;
use App\Models\PositionCandidate;
use App\Models\ProgressUpdate;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $activeMembers = Member::query()->where('status', 'active');
        $highPriorityMembers = Member::query()
            ->whereIn('note_priority', ['high', 'urgent', 'medium'])
            ->latest()
            ->take(4)
            ->get();

        $todayActivities = Activity::query()
            ->with(['members'])
            ->whereDate('scheduled_at', now()->toDateString())
            ->orderBy('scheduled_at')
            ->take(5)
            ->get();

        $upcomingActivities = Activity::query()
            ->whereDate('scheduled_at', '>=', now()->toDateString())
            ->whereDate('scheduled_at', '<=', now()->addDays(7)->toDateString())
            ->count();

        $openNotes = MemberNote::query()->where('follow_up_status', '!=', 'closed');
        $activeTargets = DevelopmentTarget::query()->where('status', 'active');
        $recentProgress = ProgressUpdate::query()
            ->with(['member'])
            ->latest()
            ->take(4)
            ->get();

        $contributionOutstanding = Contribution::query()->whereIn('status', ['unpaid', 'partial']);
        $cashBalance = (float) (CashTransaction::query()
            ->selectRaw("coalesce(sum(case when type = 'income' then amount else -amount end), 0) as balance")
            ->value('balance') ?? 0);

        $monthIncome = (float) (CashTransaction::query()
            ->where('type', 'income')
            ->whereBetween('transaction_date', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])
            ->sum('amount'));

        $monthExpense = (float) (CashTransaction::query()
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])
            ->sum('amount'));

        $openIdealPositions = IdealPosition::query()->where('status', '!=', 'filled')->count();
        $activeCandidates = PositionCandidate::query()->whereIn('status', ['candidate', 'assigned'])->count();

        $tagSignals = MemberNote::query()
            ->selectRaw('tag, count(*) as aggregate')
            ->groupBy('tag')
            ->orderByDesc('aggregate')
            ->take(4)
            ->get();

        $functionMap = DevelopmentTarget::query()
            ->selectRaw('function_name, count(*) as aggregate')
            ->whereNotNull('function_name')
            ->where('function_name', '!=', '')
            ->groupBy('function_name')
            ->orderByDesc('aggregate')
            ->take(5)
            ->get();

        $memberStatusBreakdown = Member::query()
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->orderByDesc('aggregate')
            ->get();

        $activityStatusBreakdown = Activity::query()
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->orderByDesc('aggregate')
            ->get();

        $operatorStats = [
            ['label' => 'Super Admin', 'value' => User::query()->where('role', User::ROLE_SUPER_ADMIN)->count()],
            ['label' => 'Mentor', 'value' => User::query()->where('role', User::ROLE_MENTOR)->count()],
            ['label' => 'Keuangan', 'value' => User::query()->where('role', User::ROLE_KEUANGAN)->count()],
        ];

        $memberStatusTotal = max(1, (int) $memberStatusBreakdown->sum('aggregate'));
        $activityStatusTotal = max(1, (int) $activityStatusBreakdown->sum('aggregate'));

        $chartPalette = ['#111111', '#4f46e5', '#059669', '#d97706', '#dc2626', '#64748b'];

        return view('dashboard', [
            'stats' => [
                ['label' => 'Member aktif', 'value' => (string) $activeMembers->count(), 'meta' => 'Terdata aktif di master komunitas'],
                ['label' => 'Note follow up aktif', 'value' => (string) $openNotes->count(), 'meta' => 'Masih perlu aksi dari pengurus'],
                ['label' => 'Agenda hari ini', 'value' => (string) $todayActivities->count(), 'meta' => $upcomingActivities.' agenda dalam 7 hari'],
                ['label' => 'Target aktif', 'value' => (string) $activeTargets->count(), 'meta' => 'Arah pembinaan yang berjalan'],
                ['label' => 'Saldo kas', 'value' => 'Rp '.number_format($cashBalance, 0, ',', '.'), 'meta' => $contributionOutstanding->count().' iuran belum lunas'],
                ['label' => 'Posisi ideal terbuka', 'value' => (string) $openIdealPositions, 'meta' => $activeCandidates.' kandidat aktif'],
            ],
            'focusMembers' => $highPriorityMembers,
            'todayAgenda' => $todayActivities,
            'signals' => $tagSignals,
            'recentProgress' => $recentProgress,
            'functionMap' => $functionMap,
            'memberStatusBreakdown' => $memberStatusBreakdown,
            'activityStatusBreakdown' => $activityStatusBreakdown,
            'operatorStats' => $operatorStats,
            'financeSnapshot' => [
                'outstanding_count' => $contributionOutstanding->count(),
                'outstanding_amount' => (float) $contributionOutstanding->get()->sum(fn ($item) => (float) $item->amount_due - (float) $item->amount_paid),
                'month_income' => $monthIncome,
                'month_expense' => $monthExpense,
            ],
            'chartData' => [
                'memberStatus' => $memberStatusBreakdown->values()->map(function ($item, $index) use ($memberStatusTotal, $chartPalette) {
                    return [
                        'label' => $item->status,
                        'value' => (int) $item->aggregate,
                        'percentage' => round(((int) $item->aggregate / $memberStatusTotal) * 100, 1),
                        'color' => $chartPalette[$index % count($chartPalette)],
                    ];
                }),
                'activityStatus' => $activityStatusBreakdown->values()->map(function ($item, $index) use ($activityStatusTotal, $chartPalette) {
                    return [
                        'label' => $item->status,
                        'value' => (int) $item->aggregate,
                        'percentage' => round(((int) $item->aggregate / $activityStatusTotal) * 100, 1),
                        'color' => $chartPalette[$index % count($chartPalette)],
                    ];
                }),
                'functionMap' => $functionMap->values()->map(function ($item, $index) use ($functionMap, $chartPalette) {
                    $total = max(1, (int) $functionMap->sum('aggregate'));

                    return [
                        'label' => $item->function_name,
                        'value' => (int) $item->aggregate,
                        'percentage' => round(((int) $item->aggregate / $total) * 100, 1),
                        'color' => $chartPalette[$index % count($chartPalette)],
                    ];
                }),
            ],
        ]);
    }
}
