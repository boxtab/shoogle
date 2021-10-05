<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\BuddyConfirmRequest;
use App\Http\Requests\BuddyDisconnectRequest;
use App\Http\Requests\BuddyRejectRequest;
use App\Http\Requests\BuddyRequest;
use App\Http\Resources\BuddyBidResource;
use App\Repositories\BuddyRequestRepository;
use App\Repositories\CompanyRepository;
use App\Support\ApiResponse\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseApiController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BuddyRequestController
 * @package App\Http\Controllers\API\V1
 */
class BuddyRequestController extends BaseApiController
{
    /**
     * BuddyRequestController constructor.
     * @param BuddyRequestRepository $buddyRequestRepository
     */
    public function __construct(BuddyRequestRepository $buddyRequestRepository)
    {
        $this->repository = $buddyRequestRepository;
    }

    /**
     * Friend request.
     *
     * @param BuddyRequest $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function buddyRequest(BuddyRequest $request)
    {
        try {
            $shoogleId = $request->input('shoogleId');
            $user2id = $request->input('buddyId');
            $message = $request->input('message');
            $this->repository->buddyRequest($shoogleId, $user2id, $message);
        } catch (\Exception $e) {
            return ApiResponse::returnError($e->getMessage(), $e->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return ApiResponse::returnData([], Response::HTTP_NO_CONTENT);
    }

    /**
     * Requests received.
     *
     * @param int $page
     * @param int $pageSize
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function buddyReceived(int $page, int $pageSize)
    {
        try {
            $buddyReceived = $this->repository->buddyReceived($page, $pageSize);
            $buddyReceivedResource = BuddyBidResource::collection($buddyReceived);
        } catch (\Exception $e) {
            return ApiResponse::returnError($e->getMessage(), $e->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return ApiResponse::returnData($buddyReceivedResource);
    }

    /**
     * Sent requests.
     *
     * @param int $page
     * @param int $pageSize
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function buddySent(int $page, int $pageSize)
    {
        try {
            $buddySent = $this->repository->buddySent($page, $pageSize);
            $buddySentResource = BuddyBidResource::collection($buddySent);
        } catch (\Exception $e) {
            return ApiResponse::returnError($e->getMessage(), $e->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return ApiResponse::returnData($buddySentResource);
    }

    /**
     * Accept the invitation.
     *
     * @param BuddyConfirmRequest $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function buddyConfirm(BuddyConfirmRequest $request)
    {
        try {
            $buddyRequestId = $request->input('buddyRequestId');
            $this->repository->buddyConfirm($buddyRequestId);
        } catch (\Exception $e) {
            return ApiResponse::returnError($e->getMessage(), $e->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return ApiResponse::returnData([], Response::HTTP_NO_CONTENT);
    }

    /**
     * Reject friend request.
     *
     * @param BuddyRejectRequest $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function buddyReject(BuddyRejectRequest $request)
    {
        try {
            $buddyRequestId = $request->input('buddyRequestId');
            $this->repository->buddyReject($buddyRequestId);
        } catch (\Exception $e) {
            return ApiResponse::returnError($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return ApiResponse::returnData([], Response::HTTP_NO_CONTENT);
    }

    /**
     * Leaving friends.
     *
     * @param BuddyDisconnectRequest $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function buddyDisconnect(BuddyDisconnectRequest $request)
    {
        try {
            $buddyId = $request->input('buddyId');
            $shoogleId = $request->input('shoogleId');
            $message = $request->input('message');
            $this->repository->buddyDisconnect($buddyId, $shoogleId, $message);
        } catch (\Exception $e) {
            return ApiResponse::returnError($e->getMessage(), $e->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return ApiResponse::returnData([], Response::HTTP_NO_CONTENT);
    }
}
