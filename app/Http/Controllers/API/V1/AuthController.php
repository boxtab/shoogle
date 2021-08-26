<?php

namespace App\Http\Controllers\API\V1;

use App\Constants\RoleConstant;
use App\Http\Controllers\API\BaseApiController;
use App\Http\Requests\AuthLoginRequest;
use App\Http\Requests\AuthPasswordForgotRequest;
use App\Http\Requests\AuthSignupRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\AuthResource;
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
use stdClass;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Support\ApiResponse\ApiResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Password;

/**
 * Class AuthController.
 *
 * @package App\Http\Controllers\API\V1
 */
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

    /**
     * Get a JWT via given credentials.
     *
     * @param AuthLoginRequest $request
     * @return JsonResponse
     */
    public function login(AuthLoginRequest $request)
    {
        $credentials = $request->only(['email', 'password']);
        $expirationTime = ['exp' => Carbon::now()->addDays(7)->timestamp];

        if ( ! $token = JWTAuth::attempt($credentials, $expirationTime) ) {
            $errorWrongPassword = new stdClass();
            $errorWrongPassword->password = ['Enter your password.'];
            $errorWrongPassword = collect($errorWrongPassword);

            return ApiResponse::returnError($errorWrongPassword, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $user = User::where('email', $credentials['email'])->firstOrFail();
            $authResource = new AuthResource($user);
            $authResource->setToken($token);
        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage());
        }

        return ApiResponse::returnData($authResource);
    }

    /**
     * User Authentication.
     *
     * @param AuthSignupRequest $request
     * @return JsonResponse|object
     */
    public function signup(AuthSignupRequest $request)
    {
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
            $authResource = new AuthResource($user);
            $authResource->setToken($token);
        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage());
        }

        return ApiResponse::returnData($authResource);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        $data = ['message' => 'User successfully signed out'];
        return ApiResponse::returnData($data);
    }

    /**
     * Password recovery.
     *
     * @param AuthPasswordForgotRequest $request
     * @return JsonResponse|Response
     */
    public function passwordForgot(AuthPasswordForgotRequest $request)
    {
        Password::sendResetLink();
        return ApiResponse::returnData(['password' => 'forgot']);
    }
}
