<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StatsService
{
    /**
     * Get statistics summary for a user and period
     *
     * @param User $user User requesting stats
     * @param string|null $period Period in YYYY-MM format (defaults to current month)
     * @return array Statistics data with totals by status and category
     */
    public function getStatsSummary(User $user, ?string $period): array
    {
        $period = $period ?? Carbon::now()->format('Y-m');

        [$startDate, $endDate] = $this->parsePeriod($period);

        return $user->isEmployee()
            ? $this->calculateEmployeeStats($user, $startDate, $endDate, $period)
            : $this->calculateManagerStats($startDate, $endDate, $period);
    }

    /**
     * Calculate statistics for employee (own expenses only)
     *
     * @param User $user Employee user
     * @param string $startDate Start date (Y-m-d)
     * @param string $endDate End date (Y-m-d)
     * @param string $period Period label (Y-m)
     * @return array Statistics data
     */
    private function calculateEmployeeStats(User $user, string $startDate, string $endDate, string $period): array
    {
        return $this->calculateStats(
            Expense::where('user_id', $user->id),
            $startDate,
            $endDate,
            $period
        );
    }

    /**
     * Calculate statistics for manager (all expenses)
     *
     * @param string $startDate Start date (Y-m-d)
     * @param string $endDate End date (Y-m-d)
     * @param string $period Period label (Y-m)
     * @return array Statistics data
     */
    private function calculateManagerStats(string $startDate, string $endDate, string $period): array
    {
        return $this->calculateStats(
            Expense::query(),
            $startDate,
            $endDate,
            $period
        );
    }

    /**
     * Calculate expense statistics from query
     *
     * @param mixed $query Base expense query
     * @param string $startDate Start date (Y-m-d)
     * @param string $endDate End date (Y-m-d)
     * @param string $period Period label (Y-m)
     * @return array Statistics with totals, counts, and breakdowns
     */
    private function calculateStats($query, string $startDate, string $endDate, string $period): array
    {
        // Clone base query to reuse for multiple aggregations
        $baseQuery = (clone $query)
            ->whereBetween('spent_at', [$startDate, $endDate]);

        $totalAmount = (clone $baseQuery)->sum('amount');
        $expensesCount = (clone $baseQuery)->count();

        // Group expenses by status with count and total amount
        $byStatus = (clone $baseQuery)
            ->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as amount'))
            ->groupBy('status')
            ->get()
            ->mapWithKeys(fn($item) => [
                $item->status->value => [
                    'count' => $item->count,
                    'amount' => number_format($item->amount, 2, '.', ''),
                ]
            ])
            ->toArray();

        // Group expenses by category with count and total amount
        $byCategory = (clone $baseQuery)
            ->select('category', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as amount'))
            ->groupBy('category')
            ->get()
            ->mapWithKeys(fn($item) => [
                $item->category->value => [
                    'count' => $item->count,
                    'amount' => number_format($item->amount, 2, '.', ''),
                ]
            ])
            ->toArray();

        return [
            'period' => $period,
            'total_amount' => number_format($totalAmount, 2, '.', ''),
            'expenses_count' => $expensesCount,
            'by_status' => $byStatus,
            'by_category' => $byCategory,
        ];
    }

    /**
     * Parse period string to date range
     *
     * @param string $period Period in YYYY-MM format
     * @return array [startDate, endDate] in Y-m-d format
     */
    private function parsePeriod(string $period): array
    {
        // Convert YYYY-MM to first and last day of month
        $date = Carbon::createFromFormat('Y-m', $period)->startOfMonth();

        return [
            $date->format('Y-m-d'),
            $date->copy()->endOfMonth()->format('Y-m-d'),
        ];
    }
}
