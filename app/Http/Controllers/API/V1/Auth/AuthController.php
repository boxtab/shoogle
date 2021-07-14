<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
//use Validator;

class AuthController extends Controller
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
        $tmp = (array)$request->all();

        Log::info($tmp);
        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $tokenResult = $user->createToken('Personal Access Token');

        $role = count( $user->getRoleNames() ) !== 0 ? $user->getRoleNames()[0] : null;

        $data = [
            'token' => $tokenResult->plainTextToken,
            'name_test' => $user->name,
            'rolecheck' => $role,
            'avatar_get' => $user->avatar,
        ];

        return response()->json([
            'success' => true,
            'data' => $data,
        ])->header('Authorization', 'Bearer ' . $tokenResult->plainTextToken);
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
