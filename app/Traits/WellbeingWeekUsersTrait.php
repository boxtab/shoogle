<?php

namespace App\Traits;

use App\Helpers\Helper;
use Illuminate\Http\Response;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Trait WellbeingWeekUsersTrait
 * @package App\Traits
 */
trait WellbeingWeekUsersTrait
{
    use CommunityLevelUserTrait;
    /**
     * @param int|null $departmentId
     * @param string|null $dateFrom
     * @param string|null $dateTo
     * @return array|null
     * @throws Exception
     */
    private function getUsersIDsFromDepartmentId(?int $departmentId, ?string $dateFrom, ?string $dateTo): array
    {
        if ( is_null($departmentId) ) {

            $companyId = Helper::getCompanyIdFromJWT();
            if ( is_null($companyId) ) {
                throw new Exception('The company ID was not found for the current user.', Response::HTTP_NOT_FOUND);
            }

            return $this->getUserIDs((int)$companyId, $dateFrom, $dateTo);

        } else {
            return $this->getUserIDsByDepartment($departmentId, $dateFrom, $dateTo);
        }
    }
}
