<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\API\BaseApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileStoreRequest;
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

/**
 * Class ProfileController
 * @package App\Http\Controllers\API\V1
 */
class ProfileController extends BaseApiController
{
    /**
     * ProfileController constructor.
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
//            52
//            Log::info(Auth::id());

            $this->repository->updateProfile($request);
//            if ( $request->has('profileImage') ) {
//                Log::info('Yes');
//                Log::info($request->all());
//            } else {
//                Log::info('No');
//            }
            /*
            $profile = User::where('id', Auth::id())->firstOrFail();
            $profile->update([
                'first_name' => $request->firstName,
                'last_name' => $request->lastName,
                'about' => $request->about,
            ]);

            if ( $request->has('profileImage') ) {
                $uniqueFilename = Str::uuid()->toString() . '.' . $request->file('profileImage')->extension();

                $profile->clearMediaCollection($profile->id);
                $profile->addMediaFromRequest('profileImage')
                    ->usingFileName($uniqueFilename)
                    ->toMediaCollection($profile->id);

                $mediaId = DB::table('media')->where('file_name', $uniqueFilename)->get('id')[0]->id;

                $profile->profile_image = $mediaId . '/' . $uniqueFilename;
                $profile->save();
            }
            */
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
        try {
            $profile = $this->repository->getProfile();
        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), $e->getCode());
        }

        return ApiResponse::returnData($profile);
    }
}
