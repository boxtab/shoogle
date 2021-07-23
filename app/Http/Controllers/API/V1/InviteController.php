<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseApiController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Repositories\InviteRepositoryInterface;
use App\Repositories\InviteRepository;

class InviteController extends BaseApiController
{
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
        $data = array_map('str_getcsv', file($path));
//        $csv_data = array_slice($data, 0, 4);

        $test = null;
//        $test = $this->inviteRepository->upload();

        return response()->json([
            'success' => true,
            'data' => $test//$data,
        ]);
    }
}
