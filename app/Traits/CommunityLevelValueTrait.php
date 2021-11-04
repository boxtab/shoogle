<?php

namespace App\Traits;

use App\Constants\WellbeingConstant;
use App\Helpers\HelperCalculate;
use App\Models\WellbeingScores;
use App\Repositories\WellbeingScoresRepository;
use Illuminate\Support\Facades\Log;

/**
 * Trait CommunityLevelValueTrait
 * @package App\Traits
 */
trait CommunityLevelValueTrait
{
    /**
     * Average value of indicators of wellbeing.
     *
     * @param array|null $userIDs
     * @param string $periodBegin
     * @param string $periodEnd
     * @return array
     */
    private function getValue(?array $userIDs, string $periodBegin, string $periodEnd)
    {
        $model = new WellbeingScores();
        $wellbeingScores = new WellbeingScoresRepository($model);
        $average = (array)$wellbeingScores->getAverageFromArrayUsers($userIDs, $periodBegin, $periodEnd);

        return HelperCalculate::roundingArray($average, WellbeingConstant::PRECISION);
    }
}
