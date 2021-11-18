<?php

namespace App\Traits;

use App\Helpers\HelperCompany;
use App\Helpers\HelperDepartment;
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
    private function getUserIDsByCompany(int $companyId, ?string $periodBegin, ?string $periodEnd): ?array
    {
        $userIDsCompany = HelperCompany::getArrayUserIds($companyId);
        $userIDsPeriod = HelperWellbeing::getUniqueUserIDsPerPeriod($periodBegin, $periodEnd);

        return array_intersect($userIDsCompany, $userIDsPeriod);
    }

    /**
     * Get unique identifiers of users of one department who have well-being points for a period.
     *
     * @param int $departmentId
     * @param string|null $periodBegin
     * @param string|null $periodEnd
     * @return array|null
     */
    private function getUserIDsByDepartment(int $departmentId, ?string $periodBegin, ?string $periodEnd): ?array
    {
        $userIDsDepartment = HelperDepartment::getArrayUserIds($departmentId);
        $userIDsPeriod = HelperWellbeing::getUniqueUserIDsPerPeriod($periodBegin, $periodEnd);

        return array_intersect($userIDsDepartment, $userIDsPeriod);
    }
}
