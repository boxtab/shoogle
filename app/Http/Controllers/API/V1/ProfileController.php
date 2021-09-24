<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\API\BaseApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileStoreRequest;
use App\Http\Resources\ProfileShowResource;
use App\Repositories\ProfileRepository;
use App\Support\ApiResponse\ApiResponse;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Http\Response;

/**
 * Class ProfileController
 * @package App\Http\Controllers\API\V1
 */
class ProfileController extends BaseApiController
{
    /**
     * ProfileController constructor.
     *
     * @param ProfileRepository $profileRepository
     */
    public function __construct(ProfileRepository $profileRepository)
    {
        $this->repository = $profileRepository;
    }

    /**
     * Saving a user profile.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ProfileStoreRequest $request)
    {
        try {
            $this->repository->updateProfile($request);
        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), $e->getCode());
        }

        return ApiResponse::returnData([]);
    }

    /**
     * Retrieving data from a user profile.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show()
    {
        Log::info(Auth::id());
        try {
            $profile = $this->repository->getProfile();
        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), $e->getCode());
        }

        return ApiResponse::returnData(new ProfileShowResource($profile));
    }
}
