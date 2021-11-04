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
     * @param string|null $from
     * @param string|null $to
     * @return array|null
     */
    public static function getUniqueUserIDsPerPeriod(?string $from, ?string $to): ?array
    {
        return WellbeingScores::on()
            ->when( (! is_null($from)) && (! is_null($to)), function($query) use ($from, $to) {
                return $query->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
            })
            ->groupBy('user_id')
            ->get()
            ->map(function ($item) {
                return $item->user_id;
            })
            ->toArray();
    }
}
