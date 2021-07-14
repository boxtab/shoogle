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
//use Validator;

class AuthController extends BaseApiController
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = request()->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $tokenResult = $user->createToken('Personal Access Token');
        $userResource = new UserResource($user);

        return $userResource->setToken($tokenResult->plainTextToken)
            ->response($tokenResult->plainTextToken);
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
        $credentials = request()->validate([
            'email' => 'required|email',
            'password' => 'required',
            'password2' => 'required',
        ]);

        $user = User::create([
            'name' => $credentials['email'],
            'password' => Hash::make($credentials['password']),
            'email' => $credentials['email']
        ]);

        $data = [
            'token' => $user->createToken('API Token')->plainTextToken,
        ];
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}
