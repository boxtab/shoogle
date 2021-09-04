<?php

namespace App\Http\Controllers\API;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Repositories\Repositories;
use App\Support\ApiResponse\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use stdClass;

/**
 * Base controller for returning success and failure responses.
 *
 * @package App\Http\Controllers\API
 */
class BaseApiController extends Controller
{
    /**
     * @var Repositories
     */
    protected $repository;

    /**
     * Search for a record by ID.
     *
     * @param int $id
     * @return mixed
     * @throws \Exception
     */
    protected function findRecordByID(int $id)
    {
        $record = $this->repository->find($id);

        if ( is_null( $record ) ) {
            throw new \Exception('Record not found for this ID', Response::HTTP_NOT_FOUND);
        }

        return $record;
    }
}
