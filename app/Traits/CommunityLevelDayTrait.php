<?php

namespace App\Traits;

use App\Helpers\HelperCompany;
use App\Models\WellbeingScores;
use Carbon\Carbon;

/**
 * Trait CommunityLevelDayTrait
 * @package App\Traits
 */
trait CommunityLevelDayTrait
{
    /**
     * N days ago.
     *
     * @param int|null $nDay
     * @return string
     */
    private function getNDaysAgo(?int $nDay): string
    {
        if ( is_null($nDay) ) {
            $nDay = 0;
        }

        return Carbon::now()->subDays($nDay)->toDateString();
    }

    /**
     * Get today.
     *
     * @return string
     */
    private function getToday(): string
    {
        return Carbon::now()->toDateString();
    }

    /**
     * Determine the beginning of the period.
     *
     * @param array $usersIDs
     * @param string|null $dateFrom
     * @param string|null $dateTo
     * @return string|null
     */
    private function getPeriodBegin(array $usersIDs, ?string $dateFrom, ?string $dateTo): ?string
    {
        return $this->getPeriod($usersIDs, $dateFrom, $dateTo, 'begin');
    }

    /**
     * Determine the end of the period.
     *
     * @param array $usersIDs
     * @param string|null $dateFrom
     * @param string|null $dateTo
     * @return string|null
     */
    private function getPeriodEnd(array $usersIDs, ?string $dateFrom, ?string $dateTo): ?string
    {
        return $this->getPeriod($usersIDs, $dateFrom, $dateTo, 'end');
    }

    /**
     * Defines the start or end of a period.
     *
     * @param array $usersIDs
     * @param string|null $dateFrom
     * @param string|null $dateTo
     * @param string $beginEnd
     * @return string|null
     */
    private function getPeriod(array $usersIDs, ?string $dateFrom, ?string $dateTo, string $beginEnd): ?string
    {
        switch ($beginEnd) {
            case 'begin':
                $orderBy = 'ASC';
                break;
            case 'end':
                $orderBy = 'DESC';
                break;
        }

        if ( is_null($dateTo) ) {
            $dateTo = Carbon::now()->toDateString();
        }

        $wellbeingScores = WellbeingScores::on()
            ->whereIn('user_id', $usersIDs)
            ->where('created_at', '<=', $dateTo . ' 23:59:59')
            ->when( ! is_null($dateFrom), function ($query) use ($dateFrom) {
                $query->where('created_at', '>=', $dateFrom . ' 00:00:00');
            })
            ->orderBy('created_at', $orderBy)
            ->first();

        return ( ! is_null($wellbeingScores) ) ? $wellbeingScores->created_at->toDateString() : null;
    }
}
