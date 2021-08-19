<?php

namespace App\Http\Controllers\API\V1;

use App\Constants\RoleConstant;
use App\Helpers\Helper;
use App\Http\Controllers\API\BaseApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserListResource;
use App\Http\Resources\UserProfileResource;
use App\Repositories\DepartmentRepository;
use App\Repositories\UserRepository;
use App\Support\ApiResponse\ApiResponse;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
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
        Log::info('test');
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
                return ApiResponse::returnError($e->getMessage(), $e->getCode());
            }
        }

        return ApiResponse::returnData([]);
    }

    /**
     * Display the profile user.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     * @throws Exception
     */
    public function show($id)
    {
        try {
            $record = $this->findRecordByID($id);
        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), $e->getCode());
        }

        return ApiResponse::returnData(new UserProfileResource($record));
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
            $record = $this->findRecordByID($id);
            $record->update(
                Helper::formatSnakeCase($request->all())
            );
        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), $e->getCode());
        }

        return ApiResponse::returnData([]);
    }
}
