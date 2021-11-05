<?php

namespace App\Helpers;

use App\Models\WellbeingScores;
use Illuminate\Support\Facades\DB;

/**
 * Class HelperWellbeing
 * @package App\Helpers
 */
class HelperWellbeing
{
    /**
     * Get unique user IDs with wellbeing points for a period.
     *
     * @param string|null $dateBegin
     * @param string|null $dateEnd
     * @return array|null
     */
    public static function getUniqueUserIDsPerPeriod(?string $dateBegin, ?string $dateEnd): ?array
    {
        return WellbeingScores::on()

            ->when( ! is_null($dateBegin), function($query) use ($dateBegin) {
                return $query->where('created_at', '>=', $dateBegin . ' 00:00:00');
            })

            ->when( ! is_null($dateEnd), function($query) use ($dateEnd) {
                return $query->where('created_at', '<=', $dateEnd . ' 23:59:59');
            })

            ->groupBy('user_id')
            ->get()
            ->map(function ($item) {
                return $item->user_id;
            })
            ->toArray();
    }
}
