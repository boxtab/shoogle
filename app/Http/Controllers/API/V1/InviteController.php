<?php

namespace App\Http\Controllers\API\V1;

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
use App\Repositories\InviteRepository;

class InviteController extends BaseApiController
{
    const COUNT_FIELD = 2;
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
            'file' => 'required|mimes:csv,txt',
        ]);

        if ( $validator->fails() ) {
            return $this->validatorFails( $validator->errors() );
        }

        $path = $request->file('file')->getRealPath();
        $fileCSV = array_map('str_getcsv', file($path));

        foreach ( $fileCSV as $invite ) {
            if ( count( $invite ) === self::COUNT_FIELD ) {
                continue;
            }

            if ( ! filter_var($invite[0], FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            if ( ! filter_var($invite[1], FILTER_VALIDATE_INT)) {
                continue;
            }

            if ( Invite::on()->where('email', $invite[0])->count() > 0 ) {
                continue;
            }

            if ( Company::on()->where('id', $invite[1])->count() === 0 ) {
                continue;
            }

            $company = Company::on()->where('email', $invite[0])->first();

            if ( $company !== null ) {
                $company->update([
                    'is_used' => 0,
                    'created_by' => Auth::id(),
                    'companies_id' => $invite[1],
                ]);
            } else {
                $company = Company::on()->create([
                    'email' => $invite[0],
                    'is_used' => 1,
                    'created_by' => Auth::id(),
                    'companies_id' => $invite[1],
                ]);
            }

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


        return response()->json([
            'success' => true,
            'data' => $fileCSV,
        ]);
    }
}
