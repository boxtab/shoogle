<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\WellbeingScoresAverageRequest;
use App\Http\Resources\WelbeingScoresAverageResource;
use App\Repositories\DepartmentRepository;
use App\Repositories\WellbeingScoresRepository;
use App\Support\ApiResponse\ApiResponse;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseApiController;
use Illuminate\Support\Facades\Log;

/**
 * Class WeelbeingScoresController
 * @package App\Http\Controllers\API\V1
 */
class WelbeingScoresController extends BaseApiController
{
    /**
     * WelbeingScoresController constructor.
     * @param WellbeingScoresRepository $wellbeingScoresRepository
     */
    public function __construct(WellbeingScoresRepository $wellbeingScoresRepository)
    {
        $this->repository = $wellbeingScoresRepository;
    }

    /**
     * The average of the user wellbeing scores.
     *
     * @param WellbeingScoresAverageRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function averageUser(WellbeingScoresAverageRequest $request, $id)
    {
        try {
            $this->repository->existsUser($id);
            $average = $this->repository->getAverageUser( $id, $request->input('from'), $request->input('to') );
        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), $e->getCode());
        }

        $wellbeingScoresAverageResource = new WelbeingScoresAverageResource($average);
        return ApiResponse::returnData($wellbeingScoresAverageResource);
    }

    /**
     * The average of the user shoogle scores.
     *
     * @param WellbeingScoresAverageRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function averageShoogle(WellbeingScoresAverageRequest $request, $id)
    {
        try {
            $this->repository->existsShoogle($id);
            $average = $this->repository->getAverageShoogle( $id, $request->input('from'), $request->input('to') );
        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), $e->getCode());
        }

        $wellbeingScoresAverageResource = new WelbeingScoresAverageResource($average);
        return ApiResponse::returnData($wellbeingScoresAverageResource);
    }

    /**
     * Average wellbeing scores for the company.
     *
     * @param WellbeingScoresAverageRequest $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function averageCompany(WellbeingScoresAverageRequest $request)
    {
        try {
            $average = $this->repository->getAverageCompany($request->input('from'), $request->input('to'));
        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), $e->getCode());
        }

        $wellbeingScoresAverageResource = new WelbeingScoresAverageResource($average);
        return ApiResponse::returnData($wellbeingScoresAverageResource);
    }
}
