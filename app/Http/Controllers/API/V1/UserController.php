<?php

namespace App\Http\Controllers\API\V1;

use App\Constants\RoleConstant;
use App\Http\Controllers\API\BaseApiController;
use App\Http\Controllers\Controller;
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
        $users = $this->repository->getList();

        return ApiResponse::returnData(new UserListResource($users));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $validator =  Validator::make($request->all(),[
            'email'         => 'required|email',
            'firstName'     => 'required|min:2|max:255',
            'lastName'      => 'nullable|min:2|max:255',
            'department'    => 'nullable|min:2|max:255',
        ]);

        if ( $validator->fails() ) {
            return $this->validatorFails( $validator->errors() );
        }

        $companyId = $this->getCompanyId();

        DB::transaction( function () use($request, $companyId) {
            $user = new User();
            $user->email = $request->email;
            $user->company_id = $companyId;
            $user->first_name = $request->firstName;
            $user->last_name = $request->lastName;
            $user->save();

            $user->assignRole(RoleConstant::USER);
        });

        return response()->json([
            'success' => true,
            'data' => [],
        ]);
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
            $record->update([
                'first_name' => $request->input('firstName'),
                'last_name' => $request->input('lastName'),
                'department_id' => $request->input('departmentId'),
            ]);
        } catch (Exception $e) {
            if ($e->getCode() == 23000) {
                return ApiResponse::returnError('Department does not exist.');
            } else {
                return ApiResponse::returnError($e->getMessage(), $e->getCode());
            }
        }

        return ApiResponse::returnData([]);
    }
}
