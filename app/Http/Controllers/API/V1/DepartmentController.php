<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\API\BaseApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\DepartmentCreateRequest;
use App\Http\Requests\DepartmentUpdateRequest;
use App\Http\Resources\DepartmentDetailResource;
use App\Http\Resources\DepartmentItemsResource;
use App\Http\Resources\DepartmentListResource;
use App\Models\Department;
use App\Repositories\DepartmentRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Support\ApiResponse\ApiResponse;
use Illuminate\Http\Response;
use Exception;

/**
 * Class DepartmentController
 * @package App\Http\Controllers\API\V1
 */
class DepartmentController extends BaseApiController
{
    /**
     * DepartmentController constructor.
     *
     * @param DepartmentRepository $departmentRepository
     */
    public function __construct(DepartmentRepository $departmentRepository)
    {
        $this->repository = $departmentRepository;
    }

    /**
     * Display a listing of the department.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $listDepartment = $this->repository->getList();
        $departmentListResource = new DepartmentListResource($listDepartment);

        return ApiResponse::returnData($departmentListResource);
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
            $this->repository->createDepartment($request->input('departmentName'));
        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), $e->getCode());
        }

        return ApiResponse::returnData([]);
    }

    /**
     * Display the specified resource department.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $record = $this->findRecordByID($id);

        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), $e->getCode());
        }

        return ApiResponse::returnData(new DepartmentDetailResource($record));
    }

    /**
     * Update the specified department in storage.
     *
     * @param DepartmentUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|Response
     */
    public function update(DepartmentUpdateRequest $request, $id)
    {
        try {
            $record = $this->findRecordByID($id);
            $record->update([
                'name' => $request->input('departmentName')
            ]);
        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), $e->getCode());
        }

        return ApiResponse::returnData([]);
    }

    /**
     * Remove the specified department from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse|Response
     */
    public function destroy($id)
    {
        try {
            $record = $this->findRecordByID($id);
            $record->destroy($id);
        } catch (Exception $e) {
            if ($e->getCode() == 23000) {
                return ApiResponse::returnError('The department cannot be deleted there are links to it.');
            } else {
                return ApiResponse::returnError($e->getMessage(), $e->getCode());
            }
        }

        return ApiResponse::returnData([]);
    }
}
