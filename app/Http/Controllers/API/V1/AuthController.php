<?php

namespace App\Http\Controllers\API\V1;

use App\Constants\RoleConstant;
use App\Helpers\Helper;
use App\Helpers\HelperAvatar;
use App\Helpers\HelperRole;
use App\Helpers\HelperUser;
use App\Http\Controllers\API\BaseApiController;
use App\Http\Requests\AuthCodeRequest;
use App\Http\Requests\AuthLoginRequest;
use App\Http\Requests\AuthPasswordForgotRequest;
use App\Http\Requests\AuthPasswordResetRequest;
use App\Http\Requests\AuthSignupRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\AuthLoginResource;
use App\Http\Resources\AuthResource;
use App\Http\Resources\UserResource;
use App\Mail\API\V1\ResetPasswordMail;
use App\Mail\API\V1\ResetPasswordMobileMail;
use App\Models\Invite;
use App\Repositories\AuthRepository;
use App\Services\PasswordRecoveryService;
use App\User;
use Carbon\Carbon;
//use Symfony\Component\HttpFoundation\Request;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Mail;
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
     * AuthController constructor.
     * @param AuthRepository $authRepository
     */
    public function __construct(AuthRepository $authRepository)
    {
        $this->repository = $authRepository;
    }

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
            HelperUser::checkUserDeleted($credentials['email']);

            $token = JWTAuth::attempt($credentials, $expirationTime);
            if ( ! $token ) {
                return ApiResponse::returnError(
                    ['password' => 'Invalid password!'],
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }
        } catch (JWTException $e) {
            return ApiResponse::returnError($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            return ApiResponse::returnError($e->getMessage(), $e->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $serverClient = new StreamClient(config('stream.stream_api_key'), config('stream.stream_api_secret'));
        $streamToken = $serverClient->createToken('user' . Auth::id());

        $authLoginResource = new AuthLoginResource($token);
        $authLoginResource->setStreamToken($streamToken);

        return ApiResponse::returnData($authLoginResource);
    }

    /**
     * User creation.
     *
     * @param AuthSignupRequest $request
     * @return JsonResponse|object
     */
    public function signup(AuthSignupRequest $request)
    {
        try {
            $invite = Invite::on()
                ->where('email', $request->email)
                ->firstorFail();

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
            $user = $this->repository->signup($credentials, $invite);

            $token = JWTAuth::fromUser($user);
            $authResource = new AuthResource($user);
            $authResource->setToken($token);

            $serverClient = new StreamClient(config('stream.stream_api_key'), config('stream.stream_api_secret'));
            $streamToken = $serverClient->createToken('user' . $user->id);
            $authResource->setStreamToken($streamToken);

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
        $email = $request->get('email');
        $user = User::on()
            ->where('email', '=', $email)
            ->first();

        if ( is_null( $user ) ) {
            return ApiResponse::returnError(['email' => 'There is no user with this email address.'],
                Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $roleName = HelperRole::getRoleByEmail( $email );

        if ( $roleName == RoleConstant::SUPER_ADMIN || $roleName == RoleConstant::COMPANY_ADMIN ) {

            $status = null;
            $status = Password::sendResetLink([
                'email' => $email,
            ]);

            if ( $status === Password::RESET_LINK_SENT ) {
                return ApiResponse::returnData(['status' => __($status)]);
            } else {
                return ApiResponse::returnError(__($status));
            }

        } else {

            $code = PasswordRecoveryService::getCode($email);

            // Create Password Reset Token
            DB::table('password_resets')->updateOrInsert(
                [
                    'email' => $email,
                ],
                [
                    'token' => Str::random(60),
                    'created_at' => Carbon::now()
                ]
            );

            $resetPasswordMobileMail = new ResetPasswordMobileMail($code);
            $resetPasswordMobileMail->to($email);
            Mail::send($resetPasswordMobileMail);

            return ApiResponse::returnData(['status' => 'A verification code has been sent to your email address']);

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

        if ( HelperRole::getRoleByEmail( $request->input('email') ) == 'super-admin' ||
            HelperRole::getRoleByEmail( $request->input('email') ) == 'company-admin')
        {
            if ( ! ( $passwordResets && Hash::check($request->input('token'), $passwordResets->token) ) ) {
                return ApiResponse::returnError('Invalid token.');
            }
            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
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

        if ( $passwordResets->token != $request->input('token')) {
            return ApiResponse::returnError('Invalid token user.');
        }

        User::on()
            ->where('email', '=', $request->input('email'))
            ->first()
            ->update([
                'password' => bcrypt($request->input('password')),
            ]);

        return ApiResponse::returnData(['status' => 'Data updated.']);
    }

    /**
     * Checking the validation code.
     *
     * @param AuthCodeRequest $request
     * @return JsonResponse|Response
     */
    public function codeValidation(AuthCodeRequest $request)
    {
        $code = $request->get('code');
        $recoveryCode = User::on()
            ->where('password_recovery_code', '=', $code)
            ->get();

        if ( count($recoveryCode) === 1 ) {

            User::on()
                ->where('password_recovery_code', '=', $code)
                ->update([
                    'password_recovery_code' => null
                ]);

            $email = $recoveryCode->first()->email;
            $passwordResetsCount = \App\Models\PasswordReset::on()
                ->where('email', '=', $email)
                ->count();

            if ( $passwordResetsCount !== 1 ) {
                ApiResponse::returnError('Recovery code not found.');
            }

            $passwordResets = \App\Models\PasswordReset::on()
                ->where('email', '=', $email)
                ->first();

            if ( ! is_null($passwordResets) ) {
                $token = $passwordResets->token;
//                $token = bcrypt($passwordResets->token);
//                $token = Hash::make($passwordResets->token);
                return ApiResponse::returnData(['token' => $token]);
            }

            ApiResponse::returnError('Token not found.');
        }

        return ApiResponse::returnError('Invalid recovery code.');
    }
}
