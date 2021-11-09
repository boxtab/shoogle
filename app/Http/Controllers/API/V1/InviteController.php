<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\InviteCSVRequest;
use App\Http\Requests\InviteCreateRequest;
use App\Http\Requests\InviteUpdateRequest;
use App\Http\Resources\DepartmentListResource;
use App\Http\Resources\InviteListResource;
use App\Http\Resources\InviteShowResource;
use App\Repositories\InviteRepository;
use App\Support\ApiResponse\ApiResponse;
use App\Traits\InviteCompanyTrait;
use App\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
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

/**
 * Class InviteController
 * @package App\Http\Controllers\API\V1
 */
class InviteController extends BaseApiController
{
    use InviteCompanyTrait;

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
        try {
            $listInvite = $this->repository->getList();
        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage());
        }

        return ApiResponse::returnData(new InviteListResource($listInvite));
    }

    /**
     * Creating a new invite.
     *
     * @param InviteCreateRequest $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function create(InviteCreateRequest $request)
    {
        try {
            $this->repository->create( $request->input('email'), $request->input('departmentId') );
        } catch (Exception $e) {
            if ($e->getCode() == 23000) {
                return ApiResponse::returnError('A user with this email has been invited already.');
            } else {
                return ApiResponse::returnError($e->getMessage());
            }
        }
        return ApiResponse::returnData([]);
    }

    /**
     * Update the invite in storage.
     *
     * @param InviteUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|Response
     */
    public function update(InviteUpdateRequest $request, $id)
    {
        try {
            $record = $this->findRecordByID($id);
            $this->checkCreatorInviteAndUserInCompany($id);

            $record->update([
                'email' => $request->input('email'),
                'department_id' => $request->input('departmentId'),
            ]);
        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), $e->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
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
            return ApiResponse::returnError($e->getMessage(), $e->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return ApiResponse::returnData([]);
    }

    /**
     * Display the specified resource invite.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $invite = $this->findRecordByID($id);
            $this->checkCreatorInviteAndUserInCompany($id);

            $inviteResource = new InviteShowResource($invite);
        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), $e->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return ApiResponse::returnData($inviteResource);
    }

    /**
     * Remove the invite from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse|Response
     */
    public function destroy($id)
    {
        try {
            $invite = $this->findRecordByID($id);
            $this->checkCreatorInviteAndUserInCompany($id);
            if ($invite->is_used == 1) {
                throw new Exception('Unable to delete used invite', Response::HTTP_FORBIDDEN);
            }

            $invite->destroy($id);

        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), $e->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return ApiResponse::returnData([]);
    }
}
