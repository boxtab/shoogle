<?php

namespace App\Http\Controllers\API\V1;

use App\Constants\RoleConstant;
use App\Helpers\Helper;
use App\Http\Controllers\API\BaseApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserListResource;
use App\Http\Resources\UserProfileAdminResource;
use App\Http\Resources\UserProfileFrontResource;
use App\Http\Resources\UserProfileResource;
use App\Repositories\DepartmentRepository;
use App\Repositories\UserRepository;
use App\Support\ApiResponse\ApiResponse;
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
                return ApiResponse::returnError('Violation of constraint integrity of foreign or unique key!');
            } else {
                return ApiResponse::returnError($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
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
            $record = $this->findRecordByID($id);
        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return ApiResponse::returnData(new UserProfileFrontResource($record));
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
            $record = $this->findRecordByID($id);
        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return ApiResponse::returnData(new UserProfileAdminResource($record));
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
            $credentials = $request->only(['firstName', 'lastName', 'email', 'departmentId', 'isAdminCompany']);
            $this->repository->update($user, $credentials);
        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
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
            $user->destroy($id);
        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return ApiResponse::returnData([]);
    }
}
