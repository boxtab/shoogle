<?php

namespace App\Http\Controllers\API\V1;

use App\Constants\RoleConstant;
use App\Http\Controllers\API\BaseApiController;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends BaseApiController
{
    private function rules($data)
    {
        $messages = [
            'email.required'    => 'Please enter email.',
            'email.exists'      => 'Email not registered.',
            'email.email'       => 'Please enter valid email.',
            'password.required' => 'Enter your password.',
        ];

        $validator = Validator::make($data, [
            'email'     => 'required|email|exists:users',
            'password'  => 'required',
        ], $messages);

        return $validator;
    }

    public function login(Request $request)
    {
        $validator = $this->rules($request->all());

        if ( $validator->fails() ) {
            return $this->validatorFails( $validator->errors() );
        }

        $credentials = $request->only(['email','password']);
        if ( ! Auth::attempt( $credentials ) ) {
            return response()->json([
                'success' => false,
                'errors' => ['Invalid password'],
            ], 422);
        }

        try {
            $user = User::where('email', $credentials['email'])->first();
            $token = $user->createToken('Personal Access Token')->plainTextToken;
            $userResource = new UserResource($user);
        } catch (\Exception $e) {
            return $this->globalError( $e->getMessage() );
        }

        return $userResource->setToken($token)
            ->response();
    }

    /**
     * @return JsonResponse
     */
    public function logout()
    {
        Log::info('test logout');
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
            'name' => 'required|string|unique:users|min:4|max:255|regex:/(^([a-zA-Z]+)(\d+)?$)/u',
            'email' => 'required|email|min:6|max:255',
            'password' => 'min:6|max:64|required_with:password2|same:password2',
            'password2' => 'min:6|max:64|required',
        ]);

        if ( $validator->fails() ) {
            return $this->validatorFails( $validator->errors() );
        }

        try {
            $credentials = $request->only(['name', 'email','password']);

            $user = DB::transaction( function () use ( $credentials ) {

                $user = User::create([
                    'first_name' => $credentials['name'],
                    'password' => bcrypt($credentials['password']),
                    'email' => $credentials['email'],
                ]);

                $user->assignRole(RoleConstant::USER);

                return $user;
            });

            $token = $user->createToken('Personal Access Token')->plainTextToken;
            $userResource = new UserResource($user);
        } catch (\Exception $e) {
            return $this->globalError( $e->getMessage() );
        }

        return $userResource->setToken($token)
            ->response($token)
            ->setStatusCode(200);
    }
}
