<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\Helper;
use App\Helpers\HelperRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\ShooglesEntryRequest;
use App\Http\Requests\ShooglesPaginationRequest;
use App\Http\Requests\ShooglesSearchRequest;
use App\Http\Requests\ShoogleTurnOnOffRequest;
use App\Http\Requests\ShoogleUpdateRequest;
use App\Http\Requests\ShooglesCreateRequest;
use App\Http\Resources\ShooglesListResource;
use App\Http\Resources\ShooglesResource;
use App\Http\Resources\ShooglesSearchResultResource;
use App\Http\Resources\ShooglesUserListResource;
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
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
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
            $shoogle = $this->repository->create([
                'owner_id' => Auth()->user()->id,
                'wellbeing_category_id' => $request->wellbeingCategoryId,
                'active' => $request->active,
                'title' => $request->title,
                'reminder' => $request->reminder,
                'reminder_interval' => $request->reminderInterval,
                'is_reminder' => $request->isReminder,
                'is_repetitive' => $request->isRepetitive,
//                'description' => $request->description,
                'cover_image' => $request->coverImage,
            ]);

        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return ApiResponse::returnData(['lastInsertId' => $shoogle->id]);
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
            return ApiResponse::returnError($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
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
            return ApiResponse::returnError($e->getMessage(), $e->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return ApiResponse::returnData($shooglesViewsResource);
    }

    /**
     * Set to solo mode.
     *
     * @param int|null $id
     * @return \Illuminate\Http\JsonResponse|Response
     */
    public function soloYes(int $id = null)
    {
        $this->repository->soloChange($id, true);
        return ApiResponse::returnData([], Response::HTTP_NO_CONTENT);
    }

    /**
     * Cancel solo mode.
     *
     * @param int|null $id
     * @return \Illuminate\Http\JsonResponse|Response
     */
    public function soloNo(int $id = null)
    {
        $this->repository->soloChange($id, false);
        return ApiResponse::returnData([], Response::HTTP_NO_CONTENT);
    }

    /**
     * Shoogle entry method.
     *
     * @param ShooglesEntryRequest $request
     * @return \Illuminate\Http\JsonResponse|Response
     */
    public function entry(ShooglesEntryRequest $request)
    {
        try {
            $this->repository->entry(
                Auth::id(),
                $request->input('shoogleId'),
                $request->input('note')
            );
        } catch (Exception $e) {
            if ($e->getCode() == 23000) {
                return ApiResponse::returnError('The user is already a member of the shoogle!');
            } else {
                return ApiResponse::returnError($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
        return ApiResponse::returnData([], Response::HTTP_NO_CONTENT);
    }

    /**
     * Shoogle exit method.
     *
     * @param int|null $id
     * @return \Illuminate\Http\JsonResponse|Response
     */
    public function leave(int $id = null)
    {
        $this->repository->leave($id);
        return ApiResponse::returnData([], Response::HTTP_NO_CONTENT);
    }

    /**
     * List of user shoogles.
     *
     * @param int|null $page
     * @param int|null $pageSize
     * @return \Illuminate\Http\JsonResponse|Response
     */
    public function userList(?int $page, ?int $pageSize)
    {
        if ( $page === 0 ) {
            return ApiResponse::returnError(['page' => 'Page number cannot be zero'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ( $pageSize === 0 ) {
            return ApiResponse::returnError(['pageSize' => 'PageSize number cannot be zero'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $userList = $this->repository->userList($page, $pageSize);
        $userListResource = ( ! is_null( $userList ) ) ? ShooglesUserListResource::collection($userList) : [];

        return ApiResponse::returnData($userListResource);
    }

    /**
     * Search by shoogles.
     *
     * @param ShooglesSearchRequest $request
     * @param int|null $page
     * @param int|null $pageSize
     * @return \Illuminate\Http\JsonResponse|Response
     */
    public function search(ShooglesSearchRequest $request, ?int $page, ?int $pageSize)
    {
        if ( $page === 0 ) {
            return ApiResponse::returnError(['page' => 'Page number cannot be zero'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ( $pageSize === 0 ) {
            return ApiResponse::returnError(['pageSize' => 'PageSize number cannot be zero'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $searchResult = $this->repository->search(
            $request->input('search'),
            $request->input('order'),
            $page,
            $pageSize
        );

        if ( is_null( $searchResult ) ) {
            $searchResultResource = [];
        } else {
            $searchResultResource = new ShooglesSearchResultResource( $searchResult );
            $searchResultResource->setFindCount($this->repository->getFindCount());
            $searchResultResource->setCommunityCount($this->repository->getCommunityCount());
            $searchResultResource->setBuddiesCount($this->repository->getBuddiesCount());
            $searchResultResource->setSolosCount($this->repository->getSolosCount());
        }

        return ApiResponse::returnData($searchResultResource);
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
            return ApiResponse::returnError($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return ApiResponse::returnData([]);
    }

    /**
     * Enables / disables shoogle.
     *
     * @param ShoogleTurnOnOffRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|Response
     */
    public function turnOnOff(ShoogleTurnOnOffRequest $request, $id)
    {
        try {
            $shoogle = $this->findRecordByID($id);
            $active = $request->get('active');
            $shoogle->update([
                'active' => $active,
            ]);
        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
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
                return ApiResponse::returnError($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        return ApiResponse::returnData([]);
    }
}
