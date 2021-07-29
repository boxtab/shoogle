<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\API\BaseApiController;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Constants\RoleConstant;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;

class CompanyController extends BaseApiController
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $validator =  Validator::make($request->all(),[
            'order' => [
                'required',
                Rule::in(['asc', 'desc', 'ASC', 'DESC']),
            ],
        ]);

        if ( $validator->fails() ) {
            return $this->validatorFails( $validator->errors() );
        }

        try {
            $data = DB::select(DB::raw('
                select
                    c.id as id,
                    c.name as company_name,
                    (
                        select
                            un.first_name
                        from users as un
                        left outer join model_has_roles as mhr on un.id = mhr.model_id
                        left outer join roles as r on r.id = mhr.role_id
                        where un.company_id = c.id
                          and r.name = "company-admin"
                        limit 1
                    ) as contact_person_first_name,
                    (
                        select
                            ul.last_name
                        from users as ul
                        left outer join model_has_roles as mhrl on ul.id = mhrl.model_id
                        left outer join roles as r on r.id = mhrl.role_id
                        where ul.company_id = c.id
                          and r.name = "company-admin"
                        limit 1
                    ) as contact_person_last_name,
                    (
                        select
                            un.email
                        from users as un
                        left outer join model_has_roles as mhr on un.id = mhr.model_id
                        left outer join roles as r on r.id = mhr.role_id
                        where un.company_id = c.id
                          and r.name = "company-admin"
                        limit 1
                    ) as contact_person_email,
                    (select count(uc.id) from users as uc where uc.company_id = c.id) as users_count
                from companies as c
                order by c.id
            '));


        } catch (\Exception $e) {
            return $this->globalError( $e->getMessage() );
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $data = [
            'company_name' => null,
            'first_name' => null,
            'last_name' => null,
            'email' => null,
        ];

        try {
            $company = Company::where('id', $id)->firstOrFail();
            $data['company_name'] = $company->name;

            $userAdminCompany = User::on()->
            leftJoin('model_has_roles', function($join) {
                $join->on('users.id', '=', 'model_has_roles.model_id');
            })->leftJoin('roles', function($join) {
                $join->on('roles.id', '=', 'model_has_roles.role_id');
            })
                ->where('users.company_id', $id)
                ->where('roles.name', RoleConstant::COMPANY_ADMIN)
                ->firstOrFail();
            $data['first_name'] = $userAdminCompany->first_name;
            $data['last_name'] = $userAdminCompany->last_name;
            $data['email'] = $userAdminCompany->email;

        } catch (\Exception $e) {
            return $this->globalError( $e->getMessage() );
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
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
            'email'         => 'required|email|unique:users,email|min:6|max:255',
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
            'lastName'      => 'min:2|max:255',
            'email'         => 'required|email|min:6|max:255',
            'password'      => 'required|min:6|max:64',
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
        } catch (Exception $e) {
            return $this->globalError( $e->getMessage() );
        }

        $user = Auth::user();
        $token = JWTAuth::customClaims(['company_id' => $id])->fromUser($user);

        return response()->json([
            'success' => true,
            'data' => [
                'token' => $token,
            ],
        ]);
    }
}
