<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\HelperDateTime;
use App\Http\Controllers\API\BaseApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\WellbeingWeekRequest;
use App\Http\Resources\WellbeingWeekResource;
use App\Repositories\CommunityLevelRepository;
use App\Support\ApiResponse\ApiResponse;
use App\Traits\DepartmentCompanyTrait;
use App\Traits\WellbeingWeekUsersTrait;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Http\Response;
use App\Repositories\WellbeingWeekRepository;
use Illuminate\Support\Facades\Log;

/**
 * Class WellbeingWeekController
 * @package App\Http\Controllers\API\V1
 */
class WellbeingWeekController extends  BaseApiController
{
    use WellbeingWeekUsersTrait, DepartmentCompanyTrait;

    /**
     * WellbeingWeekController constructor.
     * @param WellbeingWeekRepository $wellbeingWeekRepository
     */
    public function __construct(WellbeingWeekRepository $wellbeingWeekRepository)
    {
        $this->repository = $wellbeingWeekRepository;
    }

    /**
     * Calculating data by week.
     *
     * @param WellbeingWeekRequest $request
     * @return \Illuminate\Http\JsonResponse|Response
     */
    public function week(WellbeingWeekRequest $request)
    {
        try {
            $dateFrom = $request->get('from');
            $dateTo = $request->get('to');

            if ( ! HelperDateTime::checkDateFromLessDateTo($dateFrom, $dateTo) ) {
                throw new Exception('Date from must be less than date to.', Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $departmentId = $request->get('departmentId');
            if ( ! is_null($departmentId) ) {
                $this->isDepartmentBelongsCompany($departmentId);
            }

            $usersIDs = $this->getUsersIDsFromDepartmentId($departmentId, $dateFrom, $dateTo);

            $data = $this->repository->getDataByWeek($usersIDs, $dateFrom, $dateTo);
            $wellbeingWeekResource = new WellbeingWeekResource($data);

        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage());
        }

        return ApiResponse::returnData($wellbeingWeekResource);
    }
}
