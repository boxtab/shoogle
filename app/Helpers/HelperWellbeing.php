<?php

namespace App\Helpers;

use App\Constants\NotificationsTypeConstant;
use App\Models\NotificationToUser;
use App\Models\WellbeingScores;
use App\Scopes\NotificationToUserScope;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class HelperWellbeing
 * @package App\Helpers
 */
class HelperWellbeing
{
    /**
     * For the last days.
     */
    const LAST_DAYS = 30;

    /**
     * Low score.
     */
    const LOW_SCORE = 3;

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
        $notification = HelperNotific::checkNotification($notificationId, $userId, NotificationsTypeConstant::WELLBEING_REMIDER_ID);
        if ( is_null($notification) ) {
            return null;
        }

        $message = $notification->notification;

        return [
            'title'     => 'Well-being pulse reminder',
            'message'   => $message,
        ];
    }

    /**
     * Low level of well-being.
     *
     * @param int|null $userId
     * @return bool
     */
    public static function isLow(?int $userId): bool
    {
        if ( is_null( $userId ) ) {
            return true;
        }

        $lastDay = Carbon::now()->subDays(self::LAST_DAYS);

        $isLow = WellbeingScores::on()
            ->where('user_id', '=', $userId)
            ->where( 'created_at', '>=', $lastDay)
            ->where(function($query) {
                $query->where('social', '<=', self::LOW_SCORE)
                    ->orWhere('physical', '<=', self::LOW_SCORE)
                    ->orWhere('mental', '<=', self::LOW_SCORE)
                    ->orWhere('economical', '<=', self::LOW_SCORE)
                    ->orWhere('spiritual', '<=', self::LOW_SCORE)
                    ->orWhere('emotional', '<=', self::LOW_SCORE)
                    ->orWhere('intellectual', '<=', self::LOW_SCORE);
            })
            ->exists();

        return $isLow;
    }
}
