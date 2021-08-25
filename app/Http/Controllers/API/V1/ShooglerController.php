<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShooglerIndexRequest;
use App\Http\Resources\ShooglerListResource;
use App\Models\Shoogle;
use App\Models\UserHasShoogle;
use App\Repositories\ShooglerRepository;
use App\Repositories\ShooglesRepository;
use App\Support\ApiResponse\ApiResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\API\BaseApiController;

/**
 * Class ShooglerController
 * @package App\Http\Controllers\API\V1
 */
class ShooglerController extends BaseApiController
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
     * @param ShooglerIndexRequest $request
     * @param int $id
     * @param int $page
     * @param int $pageSize
     * @return \Illuminate\Http\JsonResponse|Response
     */
    public function index(ShooglerIndexRequest $request, int $id, int $page, int $pageSize)
    {
        try {
            $shoogle = Shoogle::find($id);
            if ( is_null( $shoogle ) ) {
                throw new \Exception('Shoogle not found for this ID', Response::HTTP_NOT_FOUND);
            }

            $shoogler = $this->repository->getList($id, $request->input('query'), $request->input('filter'));

        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), $e->getCode());
        }

        $shooglerResource = ShooglerListResource::collection($shoogler);
        return ApiResponse::returnData($shooglerResource);
    }
}