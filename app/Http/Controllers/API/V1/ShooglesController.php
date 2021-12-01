<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\Helper;
use App\Helpers\HelperCompany;
use App\Helpers\HelperMember;
use App\Helpers\HelperRankServiceClient;
use App\Helpers\HelperRequest;
use App\Helpers\HelperShoogle;
use App\Helpers\HelperShoogleViews;
use App\Helpers\HelperStream;
use App\Http\Controllers\Controller;
use App\Http\Requests\ShooglesEntryRequest;
use App\Http\Requests\ShoogleSettingRequest;
use App\Http\Requests\ShooglesPaginationRequest;
use App\Http\Requests\ShooglesSearchRequest;
use App\Http\Requests\ShoogleTurnOnOffRequest;
use App\Http\Requests\ShoogleUpdateRequest;
use App\Http\Requests\ShooglesCreateRequest;
use App\Http\Resources\ShooglesCalendarResource;
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
use App\Traits\ShoogleCompanyTrait;
use App\Traits\ShoogleValidationTrait;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseApiController;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use GetStream\StreamChat\Client as StreamClient;

/**
 * Class ShooglesController
 * @package App\Http\Controllers\API\V1
 */

class ShooglesController extends BaseApiController
{
    use ShoogleValidationTrait, ShoogleCompanyTrait;

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
        $checkReminder = $this->checkReminder(
            $request->input('reminder'),
            $request->input('reminderInterval'),
            $request->input('isReminder')
        );

        if ( ! is_null($checkReminder) ) {
            return $checkReminder;
        }

        try {
            $shoogleId = $this->repository->createShoogle([
                'owner_id'              => auth()->user()->id,
                'wellbeing_category_id' => $request->input('wellbeingCategoryId'),
                'active'                => $request->input('active'),
                'title'                 => $request->input('title'),
                'reminder'              => $request->input('reminder'),
                'reminder_interval'     => $request->input('reminderInterval'),
                'is_reminder'           => $request->input('isReminder'),
                'cover_image'           => $request->input('coverImage'),
            ]);
        } catch (\GetStream\StreamChat\StreamException $e) {
            return ApiResponse::returnError(
                'The remote service https://getstream.io responded with an error. Shoogle was not created.',
                Response::HTTP_BAD_GATEWAY);
        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return ApiResponse::returnData(['lastInsertId' => $shoogleId]);
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
            $shoogle = $this->findRecordByID($id);
            $this->checkCreatorAndUserInCompany($id);

            $shooglesResource = new ShooglesResource($shoogle);
        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), $e->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
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
            $shoogle = $this->findRecordByID($id);
            HelperShoogle::checkActive($id);
            $this->checkCreatorAndUserInCompany($shoogle->id);

            DB::transaction(function () use ($id) {
                HelperShoogleViews::increment($id, Auth::id());
//                $this->repository->incrementViews($id);
                HelperRankServiceClient::assignRank(Auth::id());
            });

            $shooglesViewsResource = new ShooglesViewsResource($shoogle);
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
//        $this->repository->soloChange($id, true);
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
//        $this->repository->soloChange($id, false);
        return ApiResponse::returnData([], Response::HTTP_NO_CONTENT);
    }

    /**
     * Shoogle entry method.
     *
     * @param ShooglesEntryRequest $request
     * @return \Illuminate\Http\JsonResponse|Response|null
     * @throws Exception
     */
    public function entry(ShooglesEntryRequest $request)
    {
        $checkReminder = $this->checkReminder(
            $request->input('reminder'),
            $request->input('reminderInterval'),
            $request->input('isReminder')
        );

        if ( ! is_null($checkReminder) ) {
            return $checkReminder;
        }

        try {
            $this->checkCreatorAndUserInCompany($request->get('shoogleId'));
            HelperShoogle::checkActive($request->get('shoogleId'));

            $this->repository->entry(
                Auth::id(),
                $request->input('shoogleId'),
                $request->input('reminder'),
                $request->input('reminderInterval'),
                $request->input('isReminder'),
                $request->input('buddy'),
                $request->input('note')
            );
        } catch (\GetStream\StreamChat\StreamException $e) {
            return ApiResponse::returnError(
                'The remote service https://getstream.io responded with an error. Unable to enter shoogle.',
                Response::HTTP_BAD_GATEWAY);
        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), $e->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return ApiResponse::returnData([], Response::HTTP_NO_CONTENT);
    }

    /**
     * Shoogle exit method.
     *
     * @param int|null $shoogleId
     * @return \Illuminate\Http\JsonResponse|Response
     * @throws Exception
     */
    public function leave(?int $shoogleId)
    {
        try {
            HelperShoogle::checkActive($shoogleId);
            $this->checkCreatorAndUserInCompany($shoogleId);
            $this->repository->leave($shoogleId);
        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage());
        }

        return ApiResponse::returnData([], Response::HTTP_NO_CONTENT);
    }

    /**
     * List of shoogles for the currently authenticated user.
     *
     * @param int|null $page
     * @param int|null $pageSize
     * @return \Illuminate\Http\JsonResponse|Response
     */
    public function listShoogleOfAuthUser(?int $page, ?int $pageSize)
    {
        if ( $page === 0 ) {
            return ApiResponse::returnError(['page' => 'Page number cannot be zero'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ( $pageSize === 0 ) {
            return ApiResponse::returnError(['pageSize' => 'PageSize number cannot be zero'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $userList = $this->repository->listShoogleOfAuthUser($page, $pageSize);
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

        try {
            $companyId = HelperCompany::getCompanyId();
            if ( is_null($companyId) ) {
                throw new Exception('The company ID for the current user was not found.', Response::HTTP_NOT_FOUND);
            }

            $searchResult = $this->repository->search(
                $companyId,
                $request->input('search'),
                $request->input('filter'),
                $page,
                $pageSize
            );

            if ( is_null($searchResult) ) {
                $searchResultResource = [];
            } else {
                $searchResultResource = new ShooglesSearchResultResource($searchResult);
                $searchResultResource->setFindCount($this->repository->getFindCount());
                $searchResultResource->setCommunityCount($this->repository->getCommunityCount());
                $searchResultResource->setBuddiesCount($this->repository->getBuddiesCount());
                $searchResultResource->setSolosCount($this->repository->getSolosCount());
            }

        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage());
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
            $this->checkCreatorAndUserInCompany($id);

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
            $this->checkCreatorAndUserInCompany($id);

            $active = $request->get('active');
            $shoogle->update([
                'active' => $active,
            ]);
        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), $e->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return ApiResponse::returnData([]);
    }

    /**
     * User calendar settings for shoogle.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|Response
     */
    public function calendar($id)
    {
        try {
            $shoogle = HelperShoogle::getShoogle($id);
            $member = HelperMember::getMember(Auth::id(), $id);

            HelperShoogle::checkActive($id);
            $this->checkCreatorAndUserInCompany($id);

            $calendar = $this->repository->getCalendar($shoogle, $member);
            $shooglesCalendarResource = new ShooglesCalendarResource($calendar);
        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), $e->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return ApiResponse::returnData($shooglesCalendarResource);
    }

    /**
     * Change shoogle member settings.
     *
     * @param ShoogleSettingRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|Response
     */
    public function setting(ShoogleSettingRequest $request, int $id)
    {
        try {
            $this->checkCreatorAndUserInCompany($id);
            HelperShoogle::getShoogle($id);
            HelperShoogle::checkActive($id);

            $member = HelperMember::getMember(Auth::id(), $id);
            $setting = $request->only(['reminder', 'reminderInterval', 'buddy', 'isReminder']);

            $this->repository->setSetting($member, $setting);
        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), $e->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return ApiResponse::returnData([], Response::HTTP_NO_CONTENT);
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
            HelperShoogle::checkActive($id);
            $this->checkCreatorAndUserInCompany($id);

            $shoogle = $this->findRecordByID($id);
            $this->repository->destroy($shoogle, $id);
        } catch (Exception $e) {
            if ($e->getCode() == 23000) {
                return ApiResponse::returnError('The shoogle cannot be deleted there are links to it.');
            } else {
                return ApiResponse::returnError($e->getMessage(), $e->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        return ApiResponse::returnData([]);
    }
}
