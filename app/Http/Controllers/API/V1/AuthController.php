<?php

namespace App\Http\Controllers\API\V1;

use App\Constants\RoleConstant;
use App\Http\Controllers\API\BaseApiController;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\Invite;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Exception;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends BaseApiController
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'signup']]);
    }

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

    /**
     * Get a JWT via given credentials.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        $validator = $this->rules($request->all());

        if ( $validator->fails() ) {
            return $this->validatorFails( $validator->errors() );
        }

        $credentials = $request->only(['email','password']);
        $expirationTime = ['exp' => Carbon::now()->addDays(7)->timestamp];

        if ( ! $token = JWTAuth::attempt($credentials, $expirationTime) ) {
//        if ( ! $token = Auth::attempt( $credentials ) ) {
            return response()->json([
                'success' => false,
                'errors' => (object)(['password' => 'Wrong password']),
            ], 422);
        }

        try {
            $user = User::where('email', $credentials['email'])->firstOrFail();
//            $token = $user->createToken('Personal Access Token')->plainTextToken;
            $userResource = new UserResource($user);
        } catch (Exception $e) {
            return $this->globalError( $e->getMessage() );
        }

        return $userResource->setToken($token)
            ->response();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function signup(Request $request)
    {
        $validator =  Validator::make($request->all(),[
            'email' => 'required|email|unique:users,email|min:6|max:255',
            'password' => 'min:6|max:64|required',
        ]);

        if ( $validator->fails() ) {
            Log::info( gettype( $validator->errors() ));
            return $this->validatorFails( $validator->errors() );
        }

        try {
            $invite = Invite::where('email', $request->email)->firstorFail();
        } catch (Exception $e) {
            return $this->getCustomValidatorErrors( ['email' => 'Email is not in the invite list'] );
        }

        if ( (int) $invite->is_used === 1 ) {
            return $this->getCustomValidatorErrors( ['email' => 'The invitation for this email has already been used.'] );
        }

        try {
            $credentials = $request->only(['name', 'email','password']);

            $user = DB::transaction( function () use ( $credentials, $invite ) {

                $user = User::create([
                    'company_id' => $invite->companies_id,
                    'password' => bcrypt($credentials['password']),
                    'email' => $credentials['email'],
                ]);

                $user->assignRole(RoleConstant::USER);

                DB::table('invites')
                    ->where('id', $invite->id)
                    ->update(['is_used' => 1]);


                return $user;
            });
            $token = JWTAuth::fromUser($user);
//            $token = $user->createToken('Personal Access Token')->plainTextToken;
            $userResource = new UserResource($user);
        } catch (Exception $e) {
            return $this->globalError( $e->getMessage() );
        }

        return $userResource->setToken($token)
            ->response($token)
            ->setStatusCode(200);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json([
            'success' => true,
            'data' => [
                'message' => 'User successfully signed out'
            ],
        ]);
    }
}
