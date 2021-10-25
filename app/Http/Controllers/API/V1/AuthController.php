<?php

namespace App\Http\Controllers\API\V1;

use App\Constants\RoleConstant;
use App\Helpers\Helper;
use App\Helpers\HelperAvatar;
use App\Http\Controllers\API\BaseApiController;
use App\Http\Requests\AuthLoginRequest;
use App\Http\Requests\AuthPasswordForgotRequest;
use App\Http\Requests\AuthPasswordResetRequest;
use App\Http\Requests\AuthSignupRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\AuthLoginResource;
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
use GetStream\StreamChat\Client as StreamClient;

/**
 * Class AuthController.
 *
 * @package App\Http\Controllers\API\V1
 */
class AuthController extends BaseApiController
{
    /**
     * Token lifetime in days.
     */
    const EXPIRATION_TIME = 30;

    /**
     * Get a JWT via given credentials.
     *
     * @param AuthLoginRequest $request
     * @return JsonResponse|Response
     * @throws \GetStream\StreamChat\StreamException
     */
    public function login(AuthLoginRequest $request)
    {
        $credentials = $request->only(['email', 'password']);
        $expirationTime = ['exp' => Carbon::now()->addDays(self::EXPIRATION_TIME)->timestamp];

        try {
            $token = JWTAuth::attempt($credentials, $expirationTime);
            if ( ! $token ) {
                return ApiResponse::returnError(
                    ['password' => 'Invalid password!'],
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }
        } catch (JWTException $e) {
            return ApiResponse::returnError($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $serverClient = new StreamClient(config('stream.stream_api_key'), config('stream.stream_api_secret'));
        $streamToken = $serverClient->createToken('user' . Auth::id());

        $authLoginResource = new AuthLoginResource($token);
        $authLoginResource->setStreamToken($streamToken);

        return ApiResponse::returnData($authLoginResource);
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
            $invite = Invite::on()->where('email', $request->email)->firstorFail();
        } catch (Exception $e) {
            return ApiResponse::returnError(
                ['email' => 'Email is not in the invite list'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        if ( (int) $invite->is_used === 1 ) {
            return ApiResponse::returnError(
                ['email' => 'The invitation for this email has already been used.'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        try {
            $credentials = $request->only(['email','password', 'firstName', 'lastName', 'about', 'profileImage']);

            $user = DB::transaction( function () use ( $credentials, $invite ) {

                $user = User::on()->create([
                    'company_id' => $invite->companies_id,
                    'department_id' => $invite->department_id,

                    'password' => bcrypt($credentials['password']),
                    'email' => $credentials['email'],
                    'first_name' => isset($credentials['firstName']) ? $credentials['firstName'] : null,
                    'last_name' => isset($credentials['lastName']) ? $credentials['lastName'] : null,
                    'about' => isset($credentials['about']) ? $credentials['about'] : null,
                    'rank' => 1,
                ]);

                $user->assignRole(RoleConstant::USER);

                DB::table('invites')
                    ->where('id', $invite->id)
                    ->update([
                        'is_used' => 1,
                        'user_id' => $user->id,
                    ]);

                if ( ! empty( $credentials['profileImage'] ) ) {
                    $profile = User::on()->where('id', '=', $user->id )->first();
                    HelperAvatar::saveAvatar($credentials['profileImage'], $profile);
                }

                return $user;
            });
            $token = JWTAuth::fromUser($user);
            $authResource = new AuthResource($user);
            $authResource->setToken($token);
        } catch (Exception $e) {
            if ($e->getCode() == 23000) {
                return ApiResponse::returnError('Foreign key error. Integrity constraint violation.');
            } else {
                return ApiResponse::returnError($e->getMessage());
            }
        }

        return ApiResponse::returnData($authResource);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse|Response
     */
    public function logout()
    {
        try {

            JWTAuth::invalidate( JWTAuth::getToken() );
            return ApiResponse::returnData('User successfully signed out');

        } catch (JWTException $exception) {
            return ApiResponse::returnError('Sorry, the user cannot be logged out', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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

        if ( $status === Password::RESET_LINK_SENT ) {
            return ApiResponse::returnData(['status' => __($status)]);
        } else {
            return ApiResponse::returnError(__($status));
        }
    }
}
