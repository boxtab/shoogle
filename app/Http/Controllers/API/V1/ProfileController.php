<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\API\BaseApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends BaseApiController
{
    /**
     * Saving a user profile.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ProfileRequest $request)
    {
        try {
            $profile = User::where('id', Auth::id())->firstOrFail();
            $profile->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'about' => $request->about,
            ]);

        } catch (Exception $e) {
            return $this->globalError( $e->getMessage() );
        }

        return response()->json([
            'success' => true,
            'data' => [
                'message' => 'Saving a user profile',
            ],
        ]);
    }

    /**
     * Retrieving data from a user profile.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show()
    {
        try {
            $profile = User::where('id', Auth::id())
                ->firstOrFail([
                    'first_name',
                    'last_name',
                    'about',
                    'profile_image',
                ]);

        } catch (Exception $e) {
            return $this->globalError( $e->getMessage() );
        }

        return response()->json([
            'success' => true,
            'data' => $profile,
        ]);
    }
}
