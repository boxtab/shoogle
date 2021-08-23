<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ShoogleUpdateRequest;
use App\Http\Requests\ShooglesCreateRequest;
use App\Http\Resources\ShooglesListResource;
use App\Http\Resources\ShooglesResource;
use App\Http\Resources\ShooglesViewsResource;
use App\Models\Buddie;
use App\Models\Company;
use App\Models\ModelHasRole;
use App\Models\Shoogle;
use App\Repositories\ShooglesRepository;
use App\Support\ApiRequest\ApiRequest;
use App\Support\ApiResponse\ApiResponse;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseApiController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * Class ShooglesController
 * @package App\Http\Controllers\API\V1
 */

class ShooglesController extends BaseApiController
{
    /**
     * ShooglesController constructor.
     * @param ShooglesRepository $shooglesRepository
     */
    public function __construct(ShooglesRepository $shooglesRepository)
    {
        $this->repository = $shooglesRepository;
    }

    /**
     * Display a listing of the shoogles.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $search = $request->has('search') ? $request->search : null;

        $shoogles = $this->repository->getList($search);
        $shooglesListResource = new ShooglesListResource($shoogles);

        return ApiResponse::returnData($shooglesListResource);
    }

    /**
     * Create a shoogle.
     *
     * @param ShooglesCreateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(ShooglesCreateRequest $request)
    {
        try {
            $this->repository->create([
                'owner_id' => Auth()->user()->id,
                'wellbeing_category_id' => $request->wellbeingCategoryId,
                'active' => $request->active,
                'title' => $request->title,
                'reminder' => $request->reminder,
                'reminder_interval' => $request->reminderInterval,
                'description' => $request->description,
                'cover_image' => $request->coverImage,
            ]);

        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), $e->getCode());
        }

        return ApiResponse::returnData([]);
    }

    /**
     * Show detailed information about a chat by ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id = null)
    {
        try {
            $shoogles = $this->findRecordByID($id);
            $shooglesResource = new ShooglesResource($shoogles);
        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), $e->getCode());
        }

        return ApiResponse::returnData($shooglesResource);
    }

    /**
     * Shoogle view.
     *
     * @param null $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function views($id = null)
    {
        try {
            $shoogles = $this->findRecordByID($id);
            $this->repository->incrementViews($id);
            $shooglesViewsResource = new ShooglesViewsResource($shoogles);
        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), $e->getCode());
        }

        return ApiResponse::returnData($shooglesViewsResource);
    }

    /**
     * Editing a shoogle.
     *
     * @param ShoogleUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function update(ShoogleUpdateRequest $request, $id)
    {
        try {
            $shoogle = $this->findRecordByID($id);
            $shoogle->update(
                Helper::formatSnakeCase($request->all())
            );
        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), $e->getCode());
        }

        return ApiResponse::returnData([]);
    }

    /**
     * Delete shoogle.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $shoogle = $this->findRecordByID($id);
            $shoogle->destroy($id);
        } catch (Exception $e) {
            if ($e->getCode() == 23000) {
                return ApiResponse::returnError('The shoogle cannot be deleted there are links to it.');
            } else {
                return ApiResponse::returnError($e->getMessage(), $e->getCode());
            }
        }

        return ApiResponse::returnData([]);
    }
}
