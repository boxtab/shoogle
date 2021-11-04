<?php

namespace App\Traits;

use App\Constants\WellbeingConstant;
use App\Helpers\HelperCalculate;
use App\Models\WellbeingScores;
use App\Repositories\WellbeingScoresRepository;

/**
 * Trait CommunityLevelDailyAverage
 * @package App\Traits
 */
trait CommunityLevelDailyAverage
{
    /**
     * Get the average for the day.
     *
     * @param array|null $userIDs
     * @param string $day
     * @return array|null
     */
    private function getDailyAverage(?array $userIDs, string $day): ?array
    {
        $model = new WellbeingScores();
        $wellbeingScores = new WellbeingScoresRepository($model);
        return (array)$wellbeingScores->getAverageFromArrayUsers($userIDs, $day, $day);
    }
}

