<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\API\BaseApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\WellbeingCategoryCreateRequest;
use App\Http\Requests\WellbeingCategoryUpdateRequest;
use App\Http\Resources\DepartmentDetailResource;
use App\Http\Resources\DepartmentListResource;
use App\Http\Resources\WellbeingCategoryResource;
use App\Models\Company;
use App\Models\WellbeingCategory;
use App\Repositories\DepartmentRepository;
use App\Repositories\WellbeingCategoryRepository;
use App\Support\ApiResponse\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Exception;

/**
 * Class WellbeingCategoryController
 * @package App\Http\Controllers\API\V1
 */
class WellbeingCategoryController extends BaseApiController
{
    /**
     * WellbeingCategoryController constructor.
     * @param WellbeingCategoryRepository $wellbeingCategoryRepository
     */
    public function __construct(WellbeingCategoryRepository $wellbeingCategoryRepository)
    {
        $this->repository = $wellbeingCategoryRepository;
    }

    /**
     * Display a listing of the wellbeing category.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $werllbeingCategory = $this->repository->getList();
        $werllbeingCategoryResource = WellbeingCategoryResource::collection($werllbeingCategory);

        return ApiResponse::returnData($werllbeingCategoryResource);
    }
    /**
     * Creating a wellbeing category.
     *
     * @param WellbeingCategoryCreateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(WellbeingCategoryCreateRequest $request)
    {
        try {
            $this->repository->create([
                'name' => $request->input('name')
            ]);
        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return ApiResponse::returnData([]);
    }

    /**
     * Display the wellbeing category resource.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $record = $this->findRecordByID($id);
        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return ApiResponse::returnData(new WellbeingCategoryResource($record));
    }

    /**
     * Update the Wellbeing Category in storage.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(WellbeingCategoryUpdateRequest $request, $id)
    {
        try {
            $wellbeingCategory = $this->findRecordByID($id);
            $wellbeingCategory->update([
                'name' => $request->input('name')
            ]);
//            $wellbeingCategoryResource = WellbeingCategoryResource::collection(WellbeingCategory::get());
        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return ApiResponse::returnData([]);
//        return ApiResponse::returnData($wellbeingCategoryResource);
    }

    /**
     * Remove the wellbeing category from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $wellbeingCategory = $this->findRecordByID($id);
            $wellbeingCategory->destroy($id);
        } catch (Exception $e) {
            if ($e->getCode() == 23000) {
                return ApiResponse::returnError('The wellbeing categories cannot be deleted there are links to it.');
            } else {
                return ApiResponse::returnError($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        return ApiResponse::returnData([]);
    }
}
