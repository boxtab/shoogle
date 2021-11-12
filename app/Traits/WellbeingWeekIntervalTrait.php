<?php

namespace App\Traits;

use App\Models\WellbeingScores;
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
     * @return string
     */
    public function getBeginInterval(array $userIds, ?string $dateFrom)
    {
        $dateMin = null;
        $dateMin = WellbeingScores::on()
            ->whereIn('user_id', $userIds)
//            ->where('created_at', '<=', $dateFrom)
            ->orderBy('created_at', 'ASC')
            ->first();

        Log::info($dateMin);

        return null;
    }
}
