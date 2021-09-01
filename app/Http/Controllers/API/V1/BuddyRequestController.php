<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\BuddyConfirmRequest;
use App\Http\Requests\BuddyRejectRequest;
use App\Http\Requests\BuddyRequest;
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
            $shoogleId = $request->input('buddyId');
            $user2id = $request->input('buddyId');
            $message = $request->input('message');
            $this->repository->buddyRequest($shoogleId, $user2id, $message);
        } catch (\Exception $e) {
            return ApiResponse::returnError($e->getMessage(), $e->getCode());
        }

        return ApiResponse::returnData([], Response::HTTP_NO_CONTENT);
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
            return ApiResponse::returnError($e->getMessage(), $e->getCode());
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
            return ApiResponse::returnError($e->getMessage(), $e->getCode());
        }

        return ApiResponse::returnData([], Response::HTTP_NO_CONTENT);
    }
}
