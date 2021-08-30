<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\RewardAssignRequest;
use App\Repositories\ProfileRepository;
use App\Repositories\RewardRepository;
use App\Support\ApiResponse\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseApiController;
use Symfony\Component\HttpFoundation\Response;
use Exception;

/**
 * Class RewardController
 * @package App\Http\Controllers\API\V1
 */
class RewardController extends BaseApiController
{
    /**
     * RewardController constructor.
     * @param RewardRepository $rewardRepository
     */
    public function __construct(RewardRepository $rewardRepository)
    {
        $this->repository = $rewardRepository;
    }

    public function assign(RewardAssignRequest $request)
    {
        try {
            $userId = $request->input('userId');
            $rewardId = $request->input('rewardId');

            $this->repository->assign($userId, $rewardId);
        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), $e->getCode());
        }

        return ApiResponse::returnData(['assign' => 'test']);
//        return ApiResponse::returnData(['assign' => 'test'], Response::HTTP_NO_CONTENT);
    }
}
