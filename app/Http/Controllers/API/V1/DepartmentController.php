<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\API\BaseApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\DepartmentCreateRequest;
use App\Http\Resources\DepartmentListResource;
use App\Models\Department;
use App\Repositories\DepartmentRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Support\ApiResponse\ApiResponse;

class DepartmentController extends BaseApiController
{
    /**
     * @var DepartmentRepository
     */
    private $departmentRepository;

    /**
     * DepartmentController constructor.
     *
     * @param DepartmentRepository $departmentRepository
     */
    public function __construct(DepartmentRepository $departmentRepository)
    {
        $this->departmentRepository = $departmentRepository;
    }

    /**
     * Display a listing of the department.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $listDepartment = $this->departmentRepository->getList();
        $departmentListResource = new DepartmentListResource($listDepartment);

        return ApiResponse::returnData($departmentListResource);

//        return $departmentListResource->response();

        /*
        $listDepartment = $this->departmentRepository->get(['id', 'company_id', 'name']);

        return response()->json([
            'success' => true,
            'data' => $listDepartment,
        ]);
        */
    }

    /**
     * Show the form for creating a new department.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(DepartmentCreateRequest $request)
    {
        try {
            $this->departmentRepository->create([
                'company_id' => $request->input('companyId'),
                'name' => $request->input('departmentName'),
            ]);
        } catch (\Exception $e) {
            return ApiResponse::returnError($e->getMessage());
        }

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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
