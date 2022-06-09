<?php

namespace App\Http\Controllers\API\V1;

use App\Constants\RoleConstant;
use App\Helpers\Helper;
use App\Helpers\HelperCompany;
use App\Http\Controllers\API\BaseApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserListResource;
use App\Http\Resources\UserProfileAdminResource;
use App\Http\Resources\UserProfileFrontResource;
use App\Http\Resources\UserProfileResource;
use App\Models\Invite;
use App\Repositories\DepartmentRepository;
use App\Repositories\UserRepository;
use App\Support\ApiResponse\ApiResponse;
use App\Traits\UserCompanyTrait;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PHPUnit\TextUI\Help;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;

/**
 * Class UserController
 * @package App\Http\Controllers\API\V1
 */
class UserController extends BaseApiController
{
    use UserCompanyTrait;

    /**
     * UserController constructor.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->repository = $userRepository;
    }

    /**
     * Display a listing of the users.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $users = $this->repository->getList();
        return ApiResponse::returnData(new UserListResource($users));
    }

    /**
     * Create user.
     *
     * @param UserCreateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(UserCreateRequest $request)
    {
        try {
            $credentials = $request->only(['email', 'firstName', 'lastName', 'departmentId']);
            $this->repository->create($credentials);
        } catch (Exception $e) {
            if ($e->getCode() == 23000) {
                return ApiResponse::returnError('Unable to create user.');
            } else {
                return ApiResponse::returnError($e->getMessage(), $e->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        return ApiResponse::returnData([]);
    }

    /**
     * Display the profile user for regular user.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     * @throws Exception
     */
    public function showFront($id)
    {
        try {
            $user = $this->findRecordByID($id);
        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), $e->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return ApiResponse::returnData(new UserProfileFrontResource($user));
    }

    /**
     * Display the profile user for admin-company and super-admin.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     * @throws Exception
     */
    public function showAdmin($id)
    {
        try {
            $user = $this->findRecordByID($id);

            $currentUserCompanyId = HelperCompany::getCompanyId();
            if ( is_null($currentUserCompanyId) ) {
                throw new Exception('The company ID for the current user was not found.', Response::HTTP_NOT_FOUND);
            }

            if ( Auth::user()->roles()->first()->name != RoleConstant::SUPER_ADMIN ) {
                $this->isUsersInCompany(Auth::id(), $id);
            }

        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), $e->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return ApiResponse::returnData(new UserProfileAdminResource($user));
    }

    /**
     * Update the profile user.
     *
     * @param UserUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function update(UserUpdateRequest $request, $id)
    {
        try {
            $user = $this->findRecordByID($id);
            $credentials = $request->only(['firstName', 'lastName', 'email', 'departmentId', 'isCompanyAdmin']);
            $this->repository->update($user, $credentials);
        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), $e->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return ApiResponse::returnData([]);
    }

    /**
     * Remove the user from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse|Response
     */
    public function destroy($id)
    {
        try {
            $user = $this->findRecordByID($id);

            if ($user->roles()->first()->name === RoleConstant::SUPER_ADMIN ||
                $user->roles()->first()->name === RoleConstant::COMPANY_ADMIN) {
                throw new Exception(
                    "It is not possible to remove the administrator.",
                    Response::HTTP_FORBIDDEN
                );
            }

            if ( Auth::user()->roles()->first()->name === RoleConstant::COMPANY_ADMIN) {
                if ( $user->company_id !== Auth::user()->company_id ) {
                    throw new Exception(
                        "The administrator of a company cannot delete a user who is not a member of this company.",
                        Response::HTTP_FORBIDDEN
                    );
                }
            }

//            $this->repository->delete( $user->id );
        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), $e->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return ApiResponse::returnData([]);
    }
}
