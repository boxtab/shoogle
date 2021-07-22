<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\API\BaseApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProfileController extends BaseApiController
{
    /**
     * Saving a user profile.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator =  Validator::make($request->all(),[
            'first_name' => 'required|min:2|max:255|regex:/(^([a-zA-Z]+)(\d+)?$)/u',
            'last_name' => 'nullable|min:2|max:255',
            'about' => 'nullable|min:2|max:16384',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // 2048 Kb
        ]);

        if ( $validator->fails() ) {
            return $this->validatorFails( $validator->errors() );
        }

        try {
            $profile = User::where('id', Auth::id())->firstOrFail();
            $profile->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'about' => $request->about,
            ]);

            if ( $request->has('profile_image') ) {
                $uniqueFilename = Str::uuid()->toString() . '.' . $request->file('profile_image')->extension();

                $profile->clearMediaCollection($profile->id);
                $profile->addMediaFromRequest('profile_image')
                    ->usingFileName($uniqueFilename)
                    ->toMediaCollection($profile->id);

                $mediaId = DB::table('media')->where('file_name', $uniqueFilename)->get('id')[0]->id;

                $profile->profile_image = $mediaId . '/' . $uniqueFilename;
                $profile->save();
            }

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
            $profile->profile_image = url('storage') . '/' . $profile->profile_image;
        } catch (Exception $e) {
            return $this->globalError( $e->getMessage() );
        }

        return response()->json([
            'success' => true,
            'data' => $profile,
        ]);
    }
}
