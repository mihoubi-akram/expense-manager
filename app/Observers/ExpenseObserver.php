<?php

namespace App\Observers;

use App\Models\Expense;
use App\Models\ExpenseLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class ExpenseObserver
{
    public function created(Expense $expense): void
    {
        ExpenseLog::create([
            'expense_id' => $expense->id,
            'user_id' => $expense->user_id,
            'from_status' => null,
            'to_status' => $expense->status->value,
        ]);

        $this->clearStatsCache($expense);
    }

    public function updated(Expense $expense): void
    {
        if ($expense->wasChanged('status')) {
            ExpenseLog::create([
                'expense_id' => $expense->id,
                'user_id' => auth()->id(),
                'from_status' => $expense->getOriginal('status'),
                'to_status' => $expense->status->value,
            ]);
        }

        $this->clearStatsCache($expense);
    }

    public function deleted(Expense $expense): void
    {
        $this->clearStatsCache($expense);
    }

    private function clearStatsCache(Expense $expense): void
    {
        $period = Carbon::parse($expense->spent_at)->format('Y-m');
        $currentPeriod = now()->format('Y-m');

        // Clear cache for the expense owner and all managers
        Cache::forget("stats:summary:{$expense->user_id}:{$period}");
        Cache::forget("stats:summary:all:{$period}");

        // If expense is from a previous month, also clear current month cache
        if ($period !== $currentPeriod) {
            Cache::forget("stats:summary:{$expense->user_id}:{$currentPeriod}");
            Cache::forget("stats:summary:all:{$currentPeriod}");
        }
    }
}
