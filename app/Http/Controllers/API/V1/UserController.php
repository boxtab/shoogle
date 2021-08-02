<?php

namespace App\Http\Controllers\API\V1;

use App\Constants\RoleConstant;
use App\Http\Controllers\API\BaseApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserListResource;
use App\Http\Resources\UserProfileResource;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;

class UserController extends BaseApiController
{
    /**
     * Returns the company ID.
     *
     * @return \Illuminate\Http\JsonResponse | int | null
     */
    private function getCompanyId()
    {
        $companyId = null;
        $roleName = Auth::user()->roles()->first()->name;

        try {
            switch ($roleName) {
                case RoleConstant::SUPER_ADMIN:
                    $payload = JWTAuth::parseToken()->getPayload();
                    $companyId = $payload->get('company_id');
                    if ( is_null( $companyId ) ) {
                        throw new \Exception('No company selected.');
                    }
                    break;
                case RoleConstant::COMPANY_ADMIN:
                    $companyId = Auth::user()->company_id;
                    break;
            }
        } catch ( \Exception $e ) {
            return response()->json([
                'success' => false,
                'data' => $e->getMessage(),
            ]);
        }

        return $companyId;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $companyId = $this->getCompanyId();

        $users = User::on()
            ->when( ! is_null( $companyId ) , function ($query) use ($companyId) {
                return $query->where('company_id', $companyId);
            })
            ->get();

        $userListResource = new UserListResource($users);

        return $userListResource->response();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        Log::info('create user');
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $user = User::where('id', $id)->firstOrFail();
        $userProfileResource = new UserProfileResource($user);

        return $userProfileResource->response();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validator =  Validator::make($request->all(),[
            'firstName'     => 'required|min:2|max:255',
            'lastName'      => 'nullable|min:2|max:255',
            'department'    => 'nullable|min:2|max:255',
        ]);

        if ( $validator->fails() ) {
            return $this->validatorFails( $validator->errors() );
        }

        User::where('id', $id)
            ->firstorFail()
            ->update([
                'first_name' => $request->firstName,
                'last_name' => $request->lastName
            ]);

        return response()->json([
            'success' => true,
            'data' => [],
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
