<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\InviteCSVRequest;
use App\Http\Requests\InviteStoreRequest;
use App\Http\Resources\DepartmentListResource;
use App\Http\Resources\InviteListResource;
use App\Repositories\InviteRepository;
use App\Support\ApiResponse\ApiResponse;
use App\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\API\V1\InviteMail;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Invite;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseApiController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Exception;

class InviteController extends BaseApiController
{
    /**
     * InviteController constructor.
     *
     * @param InviteRepository $inviteRepository
     */
    public function __construct(InviteRepository $inviteRepository)
    {
        $this->repository = $inviteRepository;
    }

    /**
     * Display a listing of the company.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function index()
    {
        $listInvite = $this->repository->getList();

        return ApiResponse::returnData(new InviteListResource($listInvite));
    }

    /**
     * Creating a new invite.
     *
     * @param InviteStoreRequest $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(InviteStoreRequest $request)
    {
        try {
            $this->repository->create( $request->input('email') );
        } catch (Exception $e) {
            if ($e->getCode() == 23000) {
                return ApiResponse::returnError('An invitation with this email address has already been downloaded.');
            } else {
                return ApiResponse::returnError($e->getMessage(), $e->getCode());
            }
        }
        return ApiResponse::returnData([]);
    }

    /**
     * Loading invites from a CSV file.
     *
     * @param InviteCSVRequest $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function upload(InviteCSVRequest $request)
    {
        try {
            $patchFile = $request->file('files')->getRealPath();
            $this->repository->upload($patchFile);
        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), $e->getCode());
        }

        return ApiResponse::returnData([]);
    }
}
