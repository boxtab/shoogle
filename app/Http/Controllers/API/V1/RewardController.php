<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\RewardAssignRequest;
use App\Http\Resources\RewardListResource;
use App\Repositories\ProfileRepository;
use App\Repositories\RewardRepository;
use App\Support\ApiResponse\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseApiController;
use Illuminate\Support\Facades\Log;
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

    /**
     * Assign a reward to a user.
     *
     * @param RewardAssignRequest $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function assign(RewardAssignRequest $request)
    {
        try {
            $userId = $request->input('userId');
            $rewardId = $request->input('rewardId');

            $this->repository->assign($userId, $rewardId);
        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), $e->getCode());
        }

        return ApiResponse::returnData([], Response::HTTP_NO_CONTENT);
    }

    /**
     * List of rewards.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function listReward()
    {
        $listReward = $this->repository->getList();
        $listRewardResource = RewardListResource::collection($listReward);

        return ApiResponse::returnData($listRewardResource);
    }
}
