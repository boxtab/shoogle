<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\API\BaseApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\DateNowEditRequest;
use App\Models\DateNow;
use App\Support\ApiResponse\ApiResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class DateNowController
 * @package App\Http\Controllers\API\V1
 */
class DateNowController extends BaseApiController
{
    /**
     * Setting the date and time for the scheduler.
     *
     * @param DateNowEditRequest $request
     * @return \Illuminate\Http\JsonResponse|Response
     */
    public function edit(DateNowEditRequest $request)
    {
        try {
            $dateTime = $request->get('dateTime');


            DB::transaction(function () use ($dateTime) {
                DateNow::on()->delete();

                DB::table('date_now')->insert(['date_time_now' => $dateTime]);
            });

        } catch ( Exception $e ) {
            return ApiResponse::returnError($e->getMessage(), $e->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return ApiResponse::returnData(['set' => $dateTime]);
    }
}
