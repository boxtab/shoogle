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
    public function login(Request $request)
    {
        $credentials = request()->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        Log::info( $credentials['password'] );

        $user = User::where('email', $credentials['email'])->first();
//        $user = $user = $request->user();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($user->createAccessToken('Personal Access Token'), ["user" => $user]);
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
