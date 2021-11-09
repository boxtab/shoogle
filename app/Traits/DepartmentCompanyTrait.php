<?php

namespace App\Traits;

use App\Helpers\HelperCompany;
use App\Models\Department;
use Illuminate\Http\Response;
use Exception;

/**
 * Trait DepartmentCompanyTrait
 * @package App\Traits
 */
trait DepartmentCompanyTrait
{
    /**
     * The requested department must be part of the user's company.
     *
     * @param int|null $departmentId
     * @throws Exception
     */
    private function isDepartmentBelongsCompany(?int $departmentId)
    {
        $currentUserCompanyId = HelperCompany::getCompanyId();
        if ( is_null($currentUserCompanyId) ) {
            throw new Exception('The company ID for the current user was not found.', Response::HTTP_NOT_FOUND);
        }

        if ( is_null($departmentId) ) {
            throw new Exception('Department ID not found.', Response::HTTP_NOT_FOUND);
        }

        $department = Department::on()->where('id', '=', $departmentId)->first();
        if ( is_null($department) ) {
            throw new Exception('Department not found in the directory.', Response::HTTP_NOT_FOUND);
        }

        $departmentCompanyId = $department->company_id;
        if ( is_null($departmentCompanyId) ) {
            throw new Exception('The department does not have a company ID.', Response::HTTP_NOT_FOUND);
        }

        if ( $currentUserCompanyId !== $departmentCompanyId ) {
            throw new Exception('The department is not part of the users company.', Response::HTTP_FORBIDDEN);
        }
    }
}
