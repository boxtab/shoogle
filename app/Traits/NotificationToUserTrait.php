<?php

namespace App\Traits;

use App\Models\NotificationToUser;
use App\Scopes\NotificationToUserScope;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Trait NotificationToUserTrait
 * @package App\Traits
 */
trait NotificationToUserTrait
{
    /**
     * Check the list of notification ids.
     *
     * @param array $listNotificationIDsRequest
     * @throws \Exception
     */
    private function checkListNotificationIDs(array $listNotificationIDsRequest)
    {
        $listNotificationIDs = NotificationToUser::on()
            ->withoutGlobalScope(NotificationToUserScope::class)
            ->where('user_id', '=', Auth::id())
            ->get()
            ->map(function ($item) {
                return $item->id;
            })
            ->toArray();

        // $array1 is a subset of $array2
        if ( ! (array_intersect($listNotificationIDsRequest, $listNotificationIDs) == $listNotificationIDsRequest) ) {
            throw new Exception("Not all elements of the resulting array exist or belong to the current user",
                Response::HTTP_NOT_FOUND);
        }
    }
}
