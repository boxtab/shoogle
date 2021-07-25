<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use stdClass;

class Foo{

}

/**
 * Base controller for returning success and failure responses.
 *
 * @package App\Http\Controllers\API
 */
class BaseApiController extends Controller
{
    protected function validatorFails( object $validatorErrors )
    {
        return response()->json([
            'success' => false,
            'errors' => replaceArraysOnStrings($validatorErrors),
        ], 422);
    }

    protected function globalError( $exceptionMessage )
    {
        return response()->json([
            'success' => false,
            'globalError' => $exceptionMessage,
        ]);
    }
}
