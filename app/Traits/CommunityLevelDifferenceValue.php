<?php

namespace App\Traits;

use App\Constants\WellbeingConstant;
use App\Helpers\HelperCalculate;
use Illuminate\Support\Facades\Log;

/**
 * Trait CommunityLevelDifferenceValue
 * @package App\Traits
 */
trait CommunityLevelDifferenceValue
{
    use CommunityLevelDailyAverage;

    /**
     * Period Difference Calculation.
     *
     * @param array|null $userIDs
     * @param string|null $periodBegin
     * @param string|null $periodEnd
     * @return array|null
     */
    private function getDifferenceValue(?array $userIDs, ?string $periodBegin, ?string $periodEnd): ?array
    {
        $dayA = $this->getDailyAverage($userIDs, $periodBegin);
        $dayB = $this->getDailyAverage($userIDs, $periodEnd);

        $percent = [];

        foreach ($dayA as $keyA => $valueA) {
            if ( $valueA !== 0 ) {
                $percent[$keyA] = ($dayB[$keyA] - $valueA) / $valueA * 100;
            } else {
                $percent[$keyA] = null;
            }
        }

        return HelperCalculate::roundingArray($percent, WellbeingConstant::PRECISION);
    }
}
