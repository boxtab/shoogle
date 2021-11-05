<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\API\BaseApiController;
use App\Http\Controllers\Controller;
use App\Support\ApiResponse\ApiResponse;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Http\Response;

class WellbeingWeekController extends  BaseApiController
{
    public function week()
    {
        try {
            null;
        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage());
        }

        return ApiResponse::returnData(['test' => 123]);
    }
}
