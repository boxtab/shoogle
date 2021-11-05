<?php

namespace App\Traits;

use App\Constants\WellbeingConstant;
use App\Helpers\HelperCalculate;
use App\Models\WellbeingScores;
use App\Repositories\WellbeingScoresRepository;

/**
 * Trait WellbeingWeekAverageTrait
 * @package App\Traits
 */
trait WellbeingWeekAverageTrait
{
    /**
     * Average well-being scores per interval.
     *
     * @param array $usersIDs
     * @param string|null $dateFrom
     * @param string|null $dateTo
     * @return array
     */
    private function getWeekAverage(array $usersIDs, ?string $dateFrom, ?string $dateTo): array
    {
        $model = new WellbeingScores();
        $wellbeingScores = new WellbeingScoresRepository($model);
        $average = (array)$wellbeingScores->getAverageFromArrayUsers($usersIDs, $dateFrom, $dateTo);

        return HelperCalculate::roundingArray($average, WellbeingConstant::PRECISION);
    }
}
