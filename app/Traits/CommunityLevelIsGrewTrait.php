<?php

namespace App\Traits;

use App\Constants\WellbeingConstant;
use App\Helpers\HelperCalculate;
use Illuminate\Support\Facades\Log;

/**
 * Trait CommunityLevelIsGrewTrait
 * @package App\Traits
 */
trait CommunityLevelIsGrewTrait
{
    /**
     * Sign of growth.
     *
     * @param array|null $userIDs
     * @param string $periodBegin
     * @param string $periodEnd
     * @return array|null
     */
    private function getIsGrew(?array $userIDs, string $periodBegin, string $periodEnd): ?array
    {
        $dayA = $this->getDailyAverage($userIDs, $periodBegin);
        $dayB = $this->getDailyAverage($userIDs, $periodEnd);

        $grew = [];

        foreach ($dayA as $keyA => $valueA) {
            if ( ($dayB[$keyA] - $valueA) == 0 ) {
                $grew[$keyA] = null;
            }

            if ( ($dayB[$keyA] - $valueA) > 0 ) {
                $grew[$keyA] = true;
            }

            if ( ($dayB[$keyA] - $valueA) < 0 ) {
                $grew[$keyA] = false;
            }
        }

        return $grew;
    }
}
