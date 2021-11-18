<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

/**
 * Trait WellbeingWeekLabelTrait
 * @package App\Traits
 */
trait WellbeingWeekLabelTrait
{
    /**
     * Returns an array of Mondays.
     *
     * @param string $beginDate
     * @param string $endDate
     * @return array
     * @throws \Exception
     */
    private function getLabel(string $beginDate, string $endDate): array
    {
        return $this->getMondaysInRange($beginDate, $endDate);
    }

    /**
     * Get all mondays within date range.
     *
     * @param string $dateFromString
     * @param string $dateToString
     * @return array
     * @throws \Exception
     */
    private function getMondaysInRange(string $dateFromString, string $dateToString): array
    {
        $dateFrom = new \DateTime($dateFromString);
        $dateTo = new \DateTime($dateToString);
        $dates = [];

        if ($dateFrom > $dateTo) {
            return $dates;
        }

        while ($dateFrom <= $dateTo) {
            $dates[] = $dateFrom->format('M d');
            $dateFrom->modify('+1 week');
        }

        return $dates;
    }
}
