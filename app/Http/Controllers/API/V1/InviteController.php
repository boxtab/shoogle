<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\InviteCSVRequest;
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
    const COUNT_FIELD = 1;

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
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(InviteCSVRequest $request)
    {
        $path = $request->file('files')->getRealPath();
        $fileCSV = array_map('str_getcsv', file($path));
        $listEmail = [];

        foreach ($fileCSV as $inviteRow) {

            if (count($inviteRow) !== self::COUNT_FIELD) {
                continue;
            }

            if (!filter_var($inviteRow[0], FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            if (Invite::on()->where('email', $inviteRow[0])->count() > 0) {
                continue;
            }

            if (User::on()->where('email', $inviteRow[0])->count() > 0) {
                continue;
            }

            $invite = Invite::on()->where('email', $inviteRow[0])->first();

            if ($invite !== null) {
                $invite->update([
                    'is_used' => 0,
                    'created_by' => Auth::id(),
                    'companies_id' => $request->companyId,
                ]);
            } else {

                $invite = new Invite();
                $invite->email = $inviteRow[0];
                $invite->is_used = 0;
                $invite->created_by = Auth::id();
                $invite->companies_id = $request->companyId;
                $invite->save();
            }

            $listEmail[] = $inviteRow[0];
        }

        if ( ! empty( $listEmail ) ) {
            $inviteMail = new InviteMail();
            foreach ($listEmail as $email) {
                $inviteMail->to($email);
                Mail::send($inviteMail);
            }
        }
        return ApiResponse::returnData([]);
    }
}
