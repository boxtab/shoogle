<?php

namespace App\Helpers;

use App\Constants\NotificationsTypeConstant;
use App\Models\NotificationToUser;
use App\Models\WellbeingScores;
use App\Scopes\NotificationToUserScope;
use Illuminate\Support\Facades\DB;

/**
 * Class HelperWellbeing
 * @package App\Helpers
 */
class HelperWellbeing
{
    /**
     * Get unique user IDs with wellbeing points for a period.
     *
     * @param string|null $dateBegin
     * @param string|null $dateEnd
     * @return array|null
     */
    public static function getUniqueUserIDsPerPeriod(?string $dateBegin, ?string $dateEnd): ?array
    {
        return WellbeingScores::on()

            ->when( ! is_null($dateBegin), function($query) use ($dateBegin) {
                return $query->where('created_at', '>=', $dateBegin . ' 00:00:00');
            })

            ->when( ! is_null($dateEnd), function($query) use ($dateEnd) {
                return $query->where('created_at', '<=', $dateEnd . ' 23:59:59');
            })

            ->groupBy('user_id')
            ->get()
            ->map(function ($item) {
                return $item->user_id;
            })
            ->toArray();
    }

    /**
     * When was the last time there were well-being points.
     *
     * @param int|null $userId
     * @return string|null
     */
    public static function getLastTime(?int $userId): ?string
    {
        if ( is_null( $userId ) ) {
            return null;
        }

        $wellbeingScores = WellbeingScores::on()
            ->where('user_id', '=', $userId)
            ->orderBy('created_at', 'DESC')
            ->first();

        if ( is_null($wellbeingScores) ) {
            return null;
        }

        return $wellbeingScores->created_at;
    }

    /**
     * Get notification wellbeing.
     *
     * @param int|null $notificationId
     * @param int|null $userId
     * @return array|null
     */
    public static function getNotification(?int $notificationId, ?int $userId): ?array
    {
        $notification = HelperNotific::checkNotification($notificationId, $userId, NotificationsTypeConstant::WELLBEING_ID);
        if ( is_null($notification) ) {
            return null;
        }

        return [
            'shoogleId' => null,
            'coverImage' => null,
            'message' => 'Well-being pulse reminder',
        ];
    }
}
