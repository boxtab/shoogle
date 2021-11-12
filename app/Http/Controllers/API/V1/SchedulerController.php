<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\API\BaseApiController;
use App\Http\Controllers\Controller;
use App\Services\NotificClientService;
use App\Support\ApiResponse\ApiResponse;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Http\Response;

/**
 * Class SchedulerController
 * @package App\Http\Controllers\API\V1
 */
class SchedulerController extends BaseApiController
{
    /**
     * Launching the scheduler.
     *
     * @return \Illuminate\Http\JsonResponse|Response
     */
    public function run()
    {
        $log = 'Scheduler error!';

        try {
            $notificClientService = new NotificClientService();
            $countSendNotific = $notificClientService->run();
            $log = "$countSendNotific notification(s) sent";
        } catch (Exception $e) {
            ApiResponse::returnError($e->getMessage(), $e->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return ApiResponse::returnData(['log' => $log]);
    }
}
