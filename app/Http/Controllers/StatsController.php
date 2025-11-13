<?php

namespace App\Http\Controllers;

use App\Http\Requests\Stats\GetStatsRequest;
use App\Http\Resources\StatsResource;
use App\Services\StatsService;
use Illuminate\Support\Facades\Cache;

class StatsController extends Controller
{
    public function __construct(private StatsService $statsService)
    {
    }

    /**
     * Get expense statistics summary for a period
     *
     * @param GetStatsRequest $request Validated request with optional period
     * @return StatsResource Statistics data with caching
     */
    public function summary(GetStatsRequest $request): StatsResource
    {
        $user = $request->user();
        $period = $request->period;

        $cacheKey = $this->getCacheKey($user->id, $user->isManager(), $period);

        // Cache stats for 60 seconds to reduce database queries
        $stats = Cache::remember($cacheKey, 60, function () use ($user, $period) {
            return $this->statsService->getStatsSummary($user, $period);
        });

        return StatsResource::make($stats);
    }

    /**
     * Generate cache key based on user role and period
     *
     * @param int $userId User ID
     * @param bool $isManager Whether user is a manager
     * @param string|null $period Period in YYYY-MM format
     * @return string Cache key
     */
    private function getCacheKey(int $userId, bool $isManager, ?string $period): string
    {
        $period = $period ?? now()->format('Y-m');

        // Managers see all expenses, employees see only their own
        return $isManager
            ? "stats:summary:all:{$period}"
            : "stats:summary:{$userId}:{$period}";
    }
}
