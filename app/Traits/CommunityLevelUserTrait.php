<?php

namespace App\Traits;

use App\Helpers\HelperCompany;
use App\Helpers\HelperWellbeing;

/**
 * Trait CommunityLevelUserTrait
 * @package App\Traits
 */
trait CommunityLevelUserTrait
{
    /**
     * Get unique identifiers of users of one company who have well-being points for a period.
     *
     * @param int $companyId
     * @param string|null $periodBegin
     * @param string|null $periodEnd
     * @return array|null
     */
    private function getUserIDs(int $companyId, ?string $periodBegin, ?string $periodEnd): ?array
    {
        $userIDsCompany = HelperCompany::getArrayUserIds($companyId);
        $userIDsPeriod = HelperWellbeing::getUniqueUserIDsPerPeriod($periodBegin, $periodEnd);

        return array_intersect($userIDsCompany, $userIDsPeriod);
    }
}
