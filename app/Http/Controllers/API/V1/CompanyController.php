<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\Helper;
use App\Http\Controllers\API\BaseApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyIndexRequest;
use App\Http\Resources\CompanyShowResource;
use App\Models\Company;
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
            $listCompany = $this->repository->getList();
        } catch (\Exception $e) {
            return ApiResponse::returnError( $e->getMessage() );
        }

        return ApiResponse::returnData($listCompany);
    }

    /**
     * Display details by company ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {

            $company = $this->findRecordByID($id);
            $adminCompany = $this->repository->getAdminByCompanyId($id);

        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), $e->getCode());
        }

        $companyShowResource = new CompanyShowResource($company);
        $companyShowResource->setAdminCompany($adminCompany);

        return ApiResponse::returnData($companyShowResource);
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
            'companyName'  => 'required|unique:companies,name|min:2|max:45',
            'firstName'    => 'required|min:2|max:255',
            'lastName'     => 'min:2|max:255',
            'email'         => 'required|email:rfc,dns|unique:users,email|min:5|max:255',
            'password'      => 'required|min:6|max:64',
        ]);

        if ( $validator->fails() ) {
            return $this->validatorFails( $validator->errors() );
        }

        try {
            DB::transaction( function () use ($request) {

                $company = Company::create([
                    'name' => $request->companyName,
                ]);

                $user = User::create([
                    'company_id'    => $company->id,
                    'first_name'    => $request->firstName,
                    'last_name'     => $request->lastName,
                    'email'         => $request->email,
                    'password'      => bcrypt($request->password),
                ]);

                $user->assignRole(RoleConstant::COMPANY_ADMIN);

            });

        } catch (\Illuminate\Database\QueryException $e) {
            return $this->globalError( $e->errorInfo );
        }

        return response()->json([
            'success' => true,
            'data' => [],
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validator =  Validator::make($request->all(),[
            'companyName'   => 'required|min:2|max:45',
            'firstName'     => 'required|min:2|max:255',
            'lastName'      => 'nullable|min:2|max:255',
            'email'         => 'required|email|min:6|max:255',
            'password'      => 'nullable|min:6|max:64',
        ]);

        if ( $validator->fails() ) {
            return $this->validatorFails( $validator->errors() );
        }

        try {
            Company::where('id', $id)->firstOrFail();

            DB::transaction( function () use ($request, $id) {

                Company::where('id', $id)->update([
                    'name' => $request->companyName,
                ]);

                $userAdminCompany = User::on()->
                    leftJoin('model_has_roles', function($join) {
                        $join->on('users.id', '=', 'model_has_roles.model_id');
                    })->leftJoin('roles', function($join) {
                        $join->on('roles.id', '=', 'model_has_roles.role_id');
                    })
                    ->where('users.company_id', $id)
                    ->where('roles.name', RoleConstant::COMPANY_ADMIN)
                    ->firstOrFail(['users.id']);

                User::where('id', $userAdminCompany->id)->update([
                    'company_id'    => $id,
                    'first_name'    => $request->firstName,
                    'last_name'     => $request->lastName,
                    'email'         => $request->email,
                    'password'      => bcrypt($request->password),
                ]);

            });

        } catch (\Illuminate\Database\QueryException $e) {
            return $this->globalError( $e->errorInfo );
        }

        return response()->json([
            'success' => true,
            'data' => [],
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $company = Company::findOrFail($id);

            DB::transaction( function () use ($company) {
                $user = User::where('company_id', $company->id)->first();
                $user->roles()->detach();

                User::where('company_id', $company->id)->delete();
                Company::where('id', $company->id)->delete();
            });

        } catch (\Exception $e) {
            return $this->globalError( $e->getMessage() );
        }

        return response()->json([
            'success' => true,
            'data' =>[],
        ]);
    }

    /**
     * Entry company.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function entry(Request $request, int $id)
    {
        try {
            Company::findOrFail($id);
            $token = Helper::pushCompanyIdToJWT($id);
        } catch (Exception $e) {
            return $this->globalError( $e->getMessage() );
        }

        return response()->json([
            'success' => true,
            'data' => [
                'token' => $token,
            ],
        ]);
    }

    /**
     * Current company.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function own()
    {
        $company = Company::where('id', Auth::user()->company_id)->first('name');
        $data = ! is_null($company) ? $company : ['name' => null];

        return ApiResponse::returnData($data);
    }
}
