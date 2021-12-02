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
            $userId = Auth::id();
            $profileImageTransmitted = $request->exists('profileImage');
            $firstName = $request->input('firstName');
            $lastName = $request->input('lastName');
            $about = $request->input('about');
            $profileImage = $request->input('profileImage');

            $this->repository->updateProfile($userId, $profileImageTransmitted, $firstName, $lastName, $about, $profileImage);
            $profile = $this->repository->getProfile( $userId );

        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), $e->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return ApiResponse::returnData(new ProfileShowResource($profile));
    }

    /**
     * Retrieving data from a user profile.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show()
    {
        try {
            if ( ! Auth::check() ) {
                throw new Exception('User is not found.', Response::HTTP_NOT_FOUND);
            }
            $profile = $this->repository->getProfile( Auth::id() );
        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), $e->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return ApiResponse::returnData(new ProfileShowResource($profile));
    }
}
