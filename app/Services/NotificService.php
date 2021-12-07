<?php

namespace App\Services;

use App\Helpers\HelperNow;
use App\Helpers\HelperRrule;
use App\Models\UserHasShoogle;
use App\Scopes\UserHasShoogleScope;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Class NotificService
 * @package App\Services
 */
class NotificService
{
    /**
     * Get a list of users needing notification.
     *
     * @return array
     */
    public function getLineUsers(): array
    {
        $userHasShoogleStatement = UserHasShoogle::on()
            ->select([
                'user_has_shoogle.id',
                'user_has_shoogle.user_id',
                'user_has_shoogle.shoogle_id',
                'user_has_shoogle.reminder',
                'user_has_shoogle.reminder_interval',
                'user_has_shoogle.last_notification',
                'user_has_shoogle.in_process'
            ])
            ->withoutGlobalScope(UserHasShoogleScope::class)
            ->leftJoin('shoogles', 'user_has_shoogle.shoogle_id', '=', 'shoogles.id')
            ->where('shoogles.active', '=', 1)
            ->whereNull('user_has_shoogle.left_at')
            ->where('user_has_shoogle.is_reminder', '=', 1)
            ->whereNotNull('user_has_shoogle.reminder')
            ->where(function ($query) {
                $query->whereNotNull('user_has_shoogle.reminder_interval')
                    ->orWhere(function ($query) {
                        $query->whereDate('user_has_shoogle.reminder', '<', HelperNow::getDateTime());
                    });
            });

        $userHasShoogle = $userHasShoogleStatement->get([
            'id',
            'user_id',
            'shoogle_id',
            'reminder',
            'reminder_interval',
            'last_notification',
            'in_process',
        ])->toArray();

        return $userHasShoogle;
    }

    /**
     * Get member ids from member array.
     *
     * @param array $lineUsers
     * @return array
     */
    public function getUserHasShoogleIds(array $lineUsers): array
    {
        return array_map(function ($item) {
            return $item['id'];
        }, $lineUsers);
    }

    /**
     * Block processed members.
     *
     * @param array $userHasShoogleIds
     */
    public function lockUserHasShoogle(array $userHasShoogleIds)
    {
        UserHasShoogle::on()
            ->whereIn('id', $userHasShoogleIds)
            ->update(['in_process' => HelperNow::getCarbon()]);
    }

    /**
     * Unblock processed members.
     *
     * @param int $userHasShoogleId
     */
    public function unlockUserHasShoogle(int $userHasShoogleId)
    {
        UserHasShoogle::on()
            ->where('id', '=', $userHasShoogleId)
            ->update(['in_process' => null]);
    }

    /**
     * Date the time of the last notification.
     *
     * @param int $userHasShoogleId
     */
    public function putNowLastNotification(int $userHasShoogleId)
    {
        UserHasShoogle::on()
            ->where('id', '=', $userHasShoogleId)
            ->update(['last_notification' => HelperNow::getCarbon()]);
    }

    /**
     * Do I need to send a notification?
     *
     * @param string $reminder
     * @param string|null $reminderInterval
     * @param string|null $lastNotification
     * @return bool
     * @throws \Recurr\Exception\InvalidWeekday
     */
    public function needToSend(string $reminder, ?string $reminderInterval, ?string $lastNotification): bool
    {
        $nowTimestamp = HelperNow::getTimestamp();
        $reminderTimestamp = strtotime($reminder);

        // if the event is single
        if ( empty( $reminderInterval ) ) {
            if ( empty( $lastNotification ) ) {
                if ( $nowTimestamp >= $reminderTimestamp ) {
                    return true;
                }
            }
        } else {
            return HelperRrule::eventHasCome($reminder, $reminderInterval, $lastNotification);
        }

        return false;
    }
}
