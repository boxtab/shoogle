<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\WellbeingScoresAverageRequest;
use App\Http\Resources\WelbeingScoresAverageResource;
use App\Support\ApiResponse\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseApiController;
use Illuminate\Support\Facades\Log;

/**
 * Class WeelbeingScoresController
 * @package App\Http\Controllers\API\V1
 */
class WeelbeingScoresController extends Controller
{
    /**
     * The average of the user wellbeing scores.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function averageUser(WellbeingScoresAverageRequest $request, $id)
    {
        Log::info('wellbeing scores');

        $wellbeingScoresAverageResource = new WelbeingScoresAverageResource([]);
        return ApiResponse::returnData($wellbeingScoresAverageResource);
    }

    /**
     * The average of the user shoogle scores.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function averageShoogle(WellbeingScoresAverageRequest $request, $id)
    {
        Log::info('shoogle');

        $wellbeingScoresAverageResource = new WelbeingScoresAverageResource([]);
        return ApiResponse::returnData($wellbeingScoresAverageResource);
    }
}
