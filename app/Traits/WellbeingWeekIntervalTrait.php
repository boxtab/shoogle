<?php

namespace App\Traits;

use App\Models\WellbeingScores;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Trait WellbeingWeekIntervalTrait
 * @package App\Traits
 */
trait WellbeingWeekIntervalTrait
{
    /**
     * Finds the start of the interval. First Monday.
     *
     * @param array $userIds
     * @param string|null $dateFrom
     * @return string|null
     */
    private function getBeginInterval(array $userIds, ?string $dateFrom): ?string
    {
        $dateMin = null;

        $wellbeingScores = WellbeingScores::on()
            ->whereIn('user_id', $userIds)
            ->when( ! is_null($dateFrom), function ($query) use ($dateFrom) {
                $query->where('created_at', '>=', Carbon::createFromFormat('Y-m-d H:i:s',  date($dateFrom) . ' 00:00:00'));
            })
            ->orderBy('created_at', 'ASC')
            ->first();

        if ( is_null($wellbeingScores) ) {
            return null;
        }

        $createdAt = $wellbeingScores->created_at;
        if ( is_null($createdAt) ) {
            return null;
        }

        $dateMin = Carbon::make($createdAt)->startOfWeek(Carbon::MONDAY);

        return $dateMin;
    }

    /**
     * Returns the end of the interval.
     *
     * @param array $userIds
     * @param string|null $dateTo
     * @return string|null
     */
    private function getEndInterval(array $userIds, ?string $dateTo): ?string
    {
        $dateMax = null;

        $wellbeingScores = WellbeingScores::on()
            ->whereIn('user_id', $userIds)
            ->when( ! is_null($dateTo), function ($query) use ($dateTo) {
                $query->where('created_at', '<=', Carbon::createFromFormat('Y-m-d H:i:s',  date($dateTo) . ' 23:59:59'));
            })
            ->orderBy('created_at', 'DESC')
            ->first();

        if ( is_null($wellbeingScores) ) {
            return null;
        }

        $createdAt = $wellbeingScores->created_at;
        if ( is_null($createdAt) ) {
            return null;
        }

        $dateMax = $createdAt;

        return $dateMax;
    }
}
