<?php

namespace App\Traits;

use App\Support\ApiResponse\ApiResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

/**
 * Trait ShoogleValidationTrait
 * @package App\Traits
 */
trait ShoogleValidationTrait
{
    /**
     * Reminder validation.
     *
     * @param string|null $reminder
     * @param string|null $reminderInterval
     * @param bool|null $isReminder
     * @return \Illuminate\Http\JsonResponse|null
     */
    private function checkReminder(?string $reminder, ?string $reminderInterval, ?bool $isReminder)
    {
        if ( $isReminder === true ) {
            if ( is_null($reminder) ) {
                return ApiResponse::returnError(['reminder' => 'The is reminder field is required.'],
                    Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        } else {
            if ( ! is_null($reminder) || ! is_null($reminderInterval) ) {
                $response = [];

                if ( ! is_null($reminder) ) {
                    $response['reminder'] = 'The reminder field must be empty.';
                }

                if ( ! is_null($reminderInterval) ) {
                    $response['reminderInterval'] = 'The reminderInterval field must be empty.';
                }

                return ApiResponse::returnError($response,
                    Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        return null;
    }
}
