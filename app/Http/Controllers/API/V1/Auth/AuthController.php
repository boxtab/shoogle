<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Http\Controllers\API\BaseApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class AuthController extends BaseApiController
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        $validator =  Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ( $validator->fails() ) {
            return $this->validatorFails( $validator->errors() );
        }

        $credentials = $request->only(['email','password']);
        if ( ! Auth::attempt( $credentials ) ) {
            return response()->json([
                'success' => false,
                'globalError' => 'Unauthorized',
            ], 401);
        }

        try {
            $user = User::where('email', $credentials['email'])->first();
            $token = $user->createToken('Personal Access Token')->plainTextToken;
            $userResource = new UserResource($user);
        } catch (\Exception $e) {
            return $this->globalError( $e->getMessage() );
        }

        return $userResource->setToken($token)
            ->response($token);
    }

    /**
     * @return JsonResponse
     */
    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'data' => [
                'message' => 'Tokens Revoked'
            ],
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function signup(Request $request)
    {
        $validator =  Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required_with:password2|same:password2',
            'password2' => 'required',
        ]);

        if ( $validator->fails() ) {
            return $this->validatorFails( $validator->errors() );
        }

        $credentials = $request->only(['email','password']);
        try {
            $user = User::create([
                'name' => $credentials['email'],
                'password' => bcrypt($credentials['password']),
                'email' => $credentials['email']
            ]);

            $token = $user->createToken('Personal Access Token')->plainTextToken;
            $userResource = new UserResource($user);
        } catch (\Exception $e) {
            return $this->globalError( $e->getMessage() );
        }

        return $userResource->setToken($token)
            ->response($token);
    }
}
