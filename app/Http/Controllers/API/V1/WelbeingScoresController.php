<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\WellbeingScoresAverageRequest;
use App\Http\Requests\WellbeingScoresStoreRequest;
use App\Http\Resources\WelbeingScoresAverageResource;
use App\Repositories\DepartmentRepository;
use App\Repositories\WellbeingScoresRepository;
use App\Support\ApiResponse\ApiResponse;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseApiController;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
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
     * Preservation of wellbeing-scores.
     *
     * @param WellbeingScoresStoreRequest $request
     * @return \Illuminate\Http\JsonResponse|Response
     */
    public function store(WellbeingScoresStoreRequest $request)
    {
        try {
            $this->repository->storeScores($request->all());

        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), $e->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return ApiResponse::returnData([]);
    }

    /**
     * The average of the user wellbeing scores for mobile user.
     *
     * @param WellbeingScoresAverageRequest $request
     * @return \Illuminate\Http\JsonResponse|Response
     */
    public function averageUserFront(WellbeingScoresAverageRequest $request)
    {
        $userID = Auth::id();
        $from = $request->input('from');
        $to = $request->input('to');
        return $this->getAverageUser($userID, $from, $to);
    }

    /**
     * The average of the user wellbeing scores for admin-company and super-admin.
     *
     * @param WellbeingScoresAverageRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|Response
     */
    public function averageUserAdmin(WellbeingScoresAverageRequest $request, $id)
    {
        $userID = $id;
        $from = $request->input('from');
        $to = $request->input('to');
        return $this->getAverageUser($userID, $from, $to);
    }

    /**
     * The average of the user wellbeing scores.
     *
     * @param int $userID
     * @param string|null $from
     * @param string|null $to
     * @return \Illuminate\Http\JsonResponse|Response
     */
    private function getAverageUser(int $userID, ?string $from, ?string $to)
    {
        try {
            $average = $this->repository->getAverageUser( $userID, $from, $to );
        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), $e->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
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
            return ApiResponse::returnError($e->getMessage(), $e->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
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
            return ApiResponse::returnError($e->getMessage(), $e->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $wellbeingScoresAverageResource = new WelbeingScoresAverageResource($average);
        return ApiResponse::returnData($wellbeingScoresAverageResource);
    }
}
