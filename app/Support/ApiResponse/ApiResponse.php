<?php


namespace App\Support\ApiResponse;

use App\Helpers\Helper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class ApiResponse
{
    /**
     * @param null $data
     * @param int $status
     * @param array $headers
     * @return JsonResponse|Response
     */
    public static function returnData($data = null, int $status = Response::HTTP_OK, array $headers = [])
    {
        $options = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

        if (!is_null($data)) {
            $response = response()->json(
                [
                    'success' => true,
                    'data' => $data,
                ],
                $status,
                $headers,
                $options
            );
        } else {
            $response = response()->noContent($status);
        }

        return $response;
    }

    /**
    * @param string $message
    * @param int $status
    * @param array $headers
    * @return JsonResponse|Response
    */
    public static function returnMessage(string $message, int $status = Response::HTTP_OK, array $headers = [])
    {
        $options = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

        if (!empty($data)) {
            $response = response()->json(
                [
                    'success' => true,
                    'message' => $message,
                ],
                $status,
                $headers,
                $options
            );
        } else {
            $response = response()->noContent($status);
        }

        return $response;
    }

    /**
     * @param null $errors
     * @param int $status
     * @param array $headers
     * @return JsonResponse
     */
    public static function returnError($errors = null, int $status = Response::HTTP_BAD_REQUEST, array $headers = []): JsonResponse
    {
        $options = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

        $keyError = ( $status === Response::HTTP_UNPROCESSABLE_ENTITY ) ? 'errors' : 'globalError';

        return response()->json(
            [
                'success' => false,
                $keyError => Helper::replaceArraysOnStrings($errors),
//                $keyError => replaceArraysOnStrings($errors),
            ],
            $status,
            $headers,
            $options
        );
    }
}
