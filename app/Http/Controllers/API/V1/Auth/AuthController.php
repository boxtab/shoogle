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

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $tokenResult = $user->createToken('Personal Access Token');

        $data = [
            'token' => $tokenResult->plainTextToken,
            'name' => $user->name,
            'role' => $user->getRoleNames()[0],
            'avatar' => $user->avatar,
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


    public function login2(Request $request)
    {
        // Validate request fields
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Show validation errors
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $credentials = request(['email', 'password']);

        if ( ! Auth::attempt( $credentials ) ) {
            return new JsonResponse(
                [
                    'success' => false,
                    'errors' => ['password' => 'Authentication error'],
                ],
                401
            );
        }

        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');

        $data = [
//            'token' => $tokenResult->accessToken,
            'token' => "268|DH9naQwjUpUHRwqRB2m12tILAegINCylHHlP1RYy", // токен, который будет использоваться при запросах, требующих авторизации
            'name' => "John Wick",
            'role' => "super-admin",                                   // описание роле смотри ниже
            'avatar' => "https://picsum.photos/200",
        ];

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}
