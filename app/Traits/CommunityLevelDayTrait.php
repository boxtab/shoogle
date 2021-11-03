<?php

namespace App\Traits;

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
}
