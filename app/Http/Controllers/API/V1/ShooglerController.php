<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShooglerIndexRequest;
use App\Repositories\ShooglerRepository;
use App\Repositories\ShooglesRepository;
use App\Support\ApiResponse\ApiResponse;
use Illuminate\Http\Request;

/**
 * Class ShooglerController
 * @package App\Http\Controllers\API\V1
 */
class ShooglerController extends Controller
{
    /**
     * ShooglerController constructor.
     * @param ShooglerRepository $shooglerRepository
     */
    public function __construct(ShooglerRepository $shooglerRepository)
    {
        $this->repository = $shooglerRepository;
    }

    /**
     * Display a listing of the shoogler.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ShooglerIndexRequest $request)
    {
        return ApiResponse::returnData([]);
    }
}
