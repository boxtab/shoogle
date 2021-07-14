<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\DocBlock\Tags\Param;

/**
 * Base controller for returning success and failure responses.
 *
 * @package App\Http\Controllers\API
 */
class BaseApiController extends Controller
{
    /**
     * Successful answer.
     *
     * @param array $data
     * @return JsonResponse
     */
    protected function getResponseSuccess(array $data)
    {
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Wrong answer.
     *
     * @param array $data
     * @return JsonResponse
     */
    protected function getResponseError(array $data)
    {
        return response()->json([
            'success' => false,
            'data' => $data,
        ]);
    }
}
