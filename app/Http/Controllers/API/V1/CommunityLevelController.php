<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\API\BaseApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\CommunityLevelStatisticRequest;
use App\Repositories\CommunityLevelRepository;
use App\Support\ApiResponse\ApiResponse;
use Illuminate\Http\Request;

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

    public function statistic(CommunityLevelStatisticRequest $request)
    {
        return ApiResponse::returnData(['test' => 1234567]);
    }
}
