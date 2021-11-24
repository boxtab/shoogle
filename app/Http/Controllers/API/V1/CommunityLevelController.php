<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Resources\CommunityLevelStatisticResource;
use App\Traits\DepartmentCompanyTrait;
use App\Traits\WellbeingWeekUsersTrait;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Helper;
use App\Helpers\HelperDateTime;
use App\Http\Controllers\API\BaseApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\CommunityLevelStatisticRequest;
use App\Repositories\CommunityLevelRepository;
use App\Support\ApiResponse\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Class CommunityLevelController
 * @package App\Http\Controllers\API\V1
 */
class CommunityLevelController extends BaseApiController
{
    use WellbeingWeekUsersTrait, DepartmentCompanyTrait;

    /**
     * CommunityLevelController constructor.
     * @param CommunityLevelRepository $communityLevelRepository
     */
    public function __construct(CommunityLevelRepository $communityLevelRepository)
    {
        $this->repository = $communityLevelRepository;
    }

    /**
     * Well-being points statistics for the selected period.
     *
     * @param CommunityLevelStatisticRequest $request
     * @return \Illuminate\Http\JsonResponse|Response
     */
    public function statistic(CommunityLevelStatisticRequest $request)
    {
        try {
            $dateFrom = $request->get('from');
            $dateTo = $request->get('to');

            if ( ! HelperDateTime::checkDateFromLessDateTo($dateFrom, $dateTo) ) {
                throw new Exception('Date from must be less than date to.', Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            if ( HelperDateTime::checkDatePair($dateFrom, $dateTo) ) {
                throw new Exception('Both dates must be filled in or both are empty.', Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $departmentId = $request->get('departmentId');
            if ( ! is_null($departmentId) ) {
                $this->isDepartmentBelongsCompany($departmentId);
            }

            $usersIDs = $this->getUsersIDsFromDepartmentId($departmentId, $dateFrom, $dateTo);

            $wellbeingCategory = $this->repository->getWellbeingCategory($usersIDs, $dateFrom, $dateTo);
            $wellbeingCategoryReource = new CommunityLevelStatisticResource($wellbeingCategory);

         } catch (Exception $e) {
             return ApiResponse::returnError($e->getMessage(), $e->getCode());
         }

         return ApiResponse::returnData($wellbeingCategoryReource);
    }
}
