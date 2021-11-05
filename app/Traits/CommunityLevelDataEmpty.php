<?php

namespace App\Traits;

use App\Helpers\HelperCompany;
use App\Models\WellbeingScores;
use Illuminate\Support\Facades\Log;

/**
 * Trait CommunityLevelDataEmpty
 * @package App\Traits
 */
trait CommunityLevelDataEmpty
{
    /**
     * Is there data to calculate.
     *
     * @param array $usersIDs
     * @param string|null $dateFrom
     * @param string|null $dateTo
     * @return bool
     */
    private function isEmptyDate(array $usersIDs, ?string $dateFrom, ?string $dateTo): bool
    {
        $idAllUsersCompany = $usersIDs;
//        $idAllUsersCompany = HelperCompany::getArrayUserIds($companyId);

        $wellbeingScoresRow = WellbeingScores::on()
            ->whereIn('user_id', $idAllUsersCompany)
            ->when( (! is_null($dateFrom)) && (! is_null($dateTo)), function($query) use ($dateFrom, $dateTo) {
                return $query->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);
            })
            ->count();

        return $wellbeingScoresRow > 0 ? false : true;
    }
}
