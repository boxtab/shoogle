<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\Helper;
use App\Helpers\HelperDateTime;
use App\Http\Controllers\Controller;
use App\Http\Requests\WellbeingScoresAverageRequest;
use App\Http\Requests\WellbeingScoresStoreRequest;
use App\Http\Resources\WelbeingScoresAverageResource;
use App\Repositories\DepartmentRepository;
use App\Repositories\WellbeingScoresRepository;
use App\Support\ApiResponse\ApiResponse;
use App\Traits\DepartmentCompanyTrait;
use App\Traits\ShoogleCompanyTrait;
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
    use ShoogleCompanyTrait, DepartmentCompanyTrait;

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
            $scores = $request->only([
                'social',
                'physical',
                'mental',
                'financial',
                'spiritual',
                'emotional',
                'intellectual',
            ]);

            $this->repository->storeScores(Auth::id(), $scores);

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
        $userId = Auth::id();
        $from = $request->input('from');
        $to = $request->input('to');
        return $this->getAverageUser($userId, $from, $to);
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
     * @param int $userId
     * @param string|null $dateFrom
     * @param string|null $dateTo
     * @return \Illuminate\Http\JsonResponse|Response
     */
    private function getAverageUser(int $userId, ?string $dateFrom, ?string $dateTo)
    {
        try {
            $this->repository->existsUser($userId);

            if ( ! HelperDateTime::checkDateFromLessDateTo($dateFrom, $dateTo) ) {
                throw new Exception('Date from must be less than date to.', Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $average = $this->repository->getAverageUser( $userId, $dateFrom, $dateTo );
        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage());
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
            $this->repository->existsShoogleAmongBlocked($id);
            $this->checkCreatorAndUserInCompany($id, true);

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
        return ApiResponse::returnData([]);
//        try {
//            $average = $this->repository->getAverageCompany($request->input('from'), $request->input('to'));
//        } catch (Exception $e) {
//            return ApiResponse::returnError($e->getMessage(), $e->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
//        }
//
//        $wellbeingScoresAverageResource = new WelbeingScoresAverageResource($average);
//        return ApiResponse::returnData($wellbeingScoresAverageResource);
    }

    /**
     * Get Arithmetic Average by Company ID.
     *
     * @param WellbeingScoresAverageRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|Response
     */
    public function getAverageCompanyId(WellbeingScoresAverageRequest $request, $id)
    {
        try {
            $this->repository->existsCompany($id);
            $this->checkCreatorAndUserInCompany($id);
            $average = $this->repository->getAverageCompanyId($id, $request->input('from'), $request->input('to'));

        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), $e->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $wellbeingScoresAverageResource = new WelbeingScoresAverageResource($average);
        return ApiResponse::returnData($wellbeingScoresAverageResource);
    }

    /**
     * Get Arithmetic Average by Department ID.
     *
     * @param WellbeingScoresAverageRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|Response
     */
    public function getAverageDepartmentId(WellbeingScoresAverageRequest $request, $id)
    {
        try {
            $this->repository->existsDepartment($id);
            $this->isDepartmentBelongsCompany($id);

            $average = $this->repository->getDepartmentCompanyId($id, $request->input('from'), $request->input('to'));
        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage());
        }
        $wellbeingScoresAverageResource = new WelbeingScoresAverageResource($average);
        return ApiResponse::returnData($wellbeingScoresAverageResource);
    }

    /**
     * Wellbeing points level.
     *
     * @return \Illuminate\Http\JsonResponse|Response
     */
    public function scoresLow()
    {
        try {
            $scoresLow = $this->repository->getScoresLow( Auth::id() );
        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), $e->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return ApiResponse::returnData(['scoresLow' => $scoresLow]);
    }
}
