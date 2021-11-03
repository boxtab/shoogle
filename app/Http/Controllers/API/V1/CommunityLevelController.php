<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\API\BaseApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\CommunityLevelStatisticRequest;
use App\Repositories\CommunityLevelRepository;
use App\Support\ApiResponse\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Exception;

/**
 * Class CommunityLevelController
 * @package App\Http\Controllers\API\V1
 */
class CommunityLevelController extends BaseApiController
{
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
             null;
         } catch (Exception $e) {
             return ApiResponse::returnError($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
         }

         return ApiResponse::returnData([]);
    }
}
