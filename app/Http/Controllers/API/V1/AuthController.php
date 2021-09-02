<?php

namespace App\Http\Controllers\API\V1;

use App\Constants\RoleConstant;
use App\Http\Controllers\API\BaseApiController;
use App\Http\Requests\AuthLoginRequest;
use App\Http\Requests\AuthPasswordForgotRequest;
use App\Http\Requests\AuthPasswordResetRequest;
use App\Http\Requests\AuthSignupRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\AuthResource;
use App\Http\Resources\UserResource;
use App\Models\Invite;
use App\User;
use Carbon\Carbon;
//use Symfony\Component\HttpFoundation\Request;
use Illuminate\Support\Facades\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Exception;
use League\Glide\Api\Api;
use stdClass;
use Symfony\Component\Console\Input\Input;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Support\ApiResponse\ApiResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

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
//        $this->middleware('auth:api', ['except' => ['login', 'signup']]);
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
     * Password reset.
     *
     * @param AuthPasswordForgotRequest $request
     * @return JsonResponse|Response
     */
    public function passwordForgot(AuthPasswordForgotRequest $request)
    {
        $status = null;

        $status = Password::sendResetLink([
            'email' => $request->get('email'),
        ]);

        if ( $status === Password::RESET_LINK_SENT ) {
            return ApiResponse::returnData(['status' => __($status)]);
        } else {
            return ApiResponse::returnError(__($status));
        }

//        return ApiResponse::returnData(
//            $status === Password::RESET_LINK_SENT
//                ? ['status' => __($status)]
//                : ['email' => __($status)]
//        );
    }

    /**
     * Password recovery.
     *
     * @param AuthPasswordResetRequest $request
     * @return JsonResponse|Response
     */
    public function passwordReset(AuthPasswordResetRequest $request)
    {
        $passwordResets = DB::table('password_resets')
            ->where('email', $request->input('email'))
            ->first();

        if ( ! ( $passwordResets && Hash::check($request->input('token'), $passwordResets->token) ) ) {
            return ApiResponse::returnError('Invalid token');
        }


        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) use ($request) {
                $user->forceFill([
                    'password' => bcrypt($password)
                ])->setRememberToken(Str::random(60));

                $user->save();
            }
        );

        return ApiResponse::returnData(
            $status == Password::PASSWORD_RESET
            ? ['status', __($status)]
            : ['email' => __($status)]
        );
    }
}
