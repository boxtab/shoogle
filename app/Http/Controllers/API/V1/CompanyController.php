<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\Helper;
use App\Helpers\HelperCompany;
use App\Http\Controllers\API\BaseApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyCreateRequest;
use App\Http\Requests\CompanyIndexRequest;
use App\Http\Requests\CompanyUpdateRequest;
use App\Http\Resources\CompanyShowResource;
use App\Models\Company;
use App\Models\ModelHasRole;
use App\Models\Role;
use App\Repositories\CompanyRepository;
use App\Repositories\DepartmentRepository;
use App\Support\ApiResponse\ApiResponse;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Constants\RoleConstant;
use Illuminate\Http\Response;

/**
 * Class CompanyController.
 *
 * @package App\Http\Controllers\API\V1
 */
class CompanyController extends BaseApiController
{
    /**
     * CompanyController constructor.
     * @param CompanyRepository $companyRepository
     */
    public function __construct(CompanyRepository $companyRepository)
    {
        $this->repository = $companyRepository;
    }

    /**
     * Display a listing of the company.
     *
     * @param CompanyIndexRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(CompanyIndexRequest $request)
    {
        try {
            $listCompany = $this->repository->getList($request->input('order'));
        } catch (\Exception $e) {
            return ApiResponse::returnError( $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR );
        }

        return ApiResponse::returnData($listCompany);
    }

    /**
     * Display details by company ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        try {
            $company = $this->findRecordByID($id);
//            $adminCompany = $this->repository->getAdminByCompanyId($id);
            $adminCompanyId = HelperCompany::getAdminIdByCompanyId($id);
            if ( ! is_null($adminCompanyId) ) {
                $adminCompany = User::on()->where('id', '=', $adminCompanyId)->first();
            } else {
                $adminCompany = null;
            }

        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $companyShowResource = new CompanyShowResource($company);
        $companyShowResource->setAdminCompany($adminCompany);

        return ApiResponse::returnData($companyShowResource);
    }

    /**
     * Create a company.
     *
     * @param CompanyCreateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(CompanyCreateRequest $request)
    {
        try {
            $credentials = $request->only(['companyName', 'firstName','lastName', 'email', 'password']);

            $user = User::on()
                ->where('email', '=', $credentials['email'])
                ->first();

            if ( ! is_null($user) ) {
                throw new Exception('This email is reserved by another user!', Response::HTTP_FORBIDDEN);
            }

            /*
            $user = User::withTrashed()
                ->where('email', '=', $credentials['email'])
                ->first();

            if ( ! is_null($user) ) {
                $modelHasRoles = ModelHasRole::on()
                    ->where('model_id', '=', $user->id)
                    ->first();
                if ( ! is_null($modelHasRoles) ) {
                    $roleAdminCompanyId = Role::on()->where('name', '=', RoleConstant::SUPER_ADMIN)->first()->id;
                    if ( $modelHasRoles->role_id == $roleAdminCompanyId ) {
                        throw new Exception('This email is reserved by another user!', Response::HTTP_FORBIDDEN);
                    }
                }
            }
            */
            $this->repository->create($credentials);
        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return ApiResponse::returnData([]);
    }

    /**
     * Editing company.
     *
     * @param CompanyUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|Response
     */
    public function update(CompanyUpdateRequest $request, $id)
    {
        try {
            $company = $this->findRecordByID($id);

            $credentials = $request->only(['companyName', 'firstName','lastName', 'email', 'password']);
            $this->repository->update($company, $credentials);

        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return ApiResponse::returnData([]);
    }

    /**
     * Deleting a company.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse|Response
     */
    public function destroy($id)
    {
        try {
            $company = $this->findRecordByID($id);
            $this->repository->destroy($company);
        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return ApiResponse::returnData([]);
    }

    /**
     * Entry company.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function entry(int $id)
    {
        try {
            $this->findRecordByID($id);
            $token = Helper::pushCompanyIdToJWT($id);
        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return ApiResponse::returnData(['token' => $token]);
    }

    /**
     * Current company.
     *
     * @return \Illuminate\Http\JsonResponse|Response
     */
    public function own()
    {
        $company = Company::on()
            ->where('id', '=', Auth::user()->company_id)
            ->first('name');

        $data = ! is_null($company) ? $company : ['name' => null];

        return ApiResponse::returnData($data);
    }
}
