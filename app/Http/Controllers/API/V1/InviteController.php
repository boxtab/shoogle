<?php

namespace App\Http\Controllers\API\V1;

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
use App\Repositories\InviteRepositoryInterface;
//use App\Repositories\InviteRepository;
use Exception;

class InviteController extends BaseApiController
{
    const COUNT_FIELD = 1;
    /**
     * @var InviteRepositoryInterface
     */
    private $inviteRepository;

    /**
     * InviteController constructor.
     *
     * @param InviteRepositoryInterface $inviteRepository
     */
//    private function __construct(InviteRepositoryInterface $inviteRepository)
//    {
//        $this->inviteRepository = $inviteRepository;
//    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator =  Validator::make($request->all(),[
            'files' => 'required|mimes:csv,txt',
            'companyId' => 'integer'
        ]);

        if ( $validator->fails() ) {
            return $this->validatorFails( $validator->errors() );
        }

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

//            Invite::on()->updateOrCreate(
//                [
//                    'email' =>  $invite[0]
//                ],
//                [
//                    'is_used' => 0,
//                    'created_by' => Auth::id(),
//                    'companies_id' => $invite[1]
//                ]
//            );

        }

        if ( ! empty( $listEmail ) ) {
            $inviteMail = new InviteMail();
            foreach ($listEmail as $email) {
                $inviteMail->to($email);
                Mail::send($inviteMail);
            }
        }

        return response()->json([
            'success' => true,
            'data' => [],
        ]);
    }
}
