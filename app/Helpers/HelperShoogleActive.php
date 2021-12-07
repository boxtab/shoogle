<?php

namespace App\Helpers;

use App\Models\Shoogle;
use App\Services\StreamService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Class HelperShoogleActive
 * @package App\Helpers
 */
class HelperShoogleActive
{
    /**
     * Number of days of last activity.
     */
    const NUMBER_DAYS = 30;

    /**
     * Returns the time of the last chat message.
     *
     * @param string|null $shoogleId
     * @param bool $isTimestamp
     * @return false|string|null
     */
    public static function getLatDateTime(?string $shoogleId, $isTimestamp = true)
    {
        if ( is_null($shoogleId) ) {
            return null;
        }

        $shoogle = Shoogle::on()->where('id', '=', $shoogleId)->first();
        if ( is_null($shoogle) ) {
            return null;
        }

        $shoogleChatId = $shoogle->chat_id;
        if ( is_null($shoogleChatId) ) {
            return null;
        }

//        $shoogleId = 197;
//        $shoogleChatId = 'shoogleCommunity197';

        try {
            $streamService = new StreamService($shoogleId);
            $lastDateRaw = $streamService->getChannelLastMessageDateAtString($shoogleChatId);
        } catch (\GetStream\StreamChat\StreamException $e) {
            return null;
        }

        if ( is_null($lastDateRaw) ) {
            return null;
        }

        $lastDateTimestamp = strtotime($lastDateRaw);

        return $isTimestamp ? $lastDateTimestamp : date('Y-m-d H:i:s', $lastDateTimestamp);
    }

    /**
     * Returns true if shoogle is active.
     *
     * @param int|null $shoogleId
     * @return bool
     */
    public static function isActive(?int $shoogleId): bool
    {
        $lastDateTime = self::getLatDateTime($shoogleId);
        if ( is_null($lastDateTime) ) {
            return false;
        }

        $nDaysAgo = Carbon::now()->subDays(self::NUMBER_DAYS)->getTimestamp();

        return $nDaysAgo < $lastDateTime ? true : false;
    }

    /**
     * Counts active / inactive shoogles.
     *
     * @param array|null $shoogleIds
     * @param bool $isActive
     * @return int
     */
    private static function countActiveInActive(?array $shoogleIds, bool $isActive = true): int
    {
        if ( is_null($shoogleIds) ) {
            return 0;
        }

        $counter = 0;
        foreach ($shoogleIds as $shoogleId) {
            $active = self::isActive($shoogleId);
            if ($active && $isActive) {
                $counter++;
            }

            if ( ( ! $active ) && ( ! $isActive ) ) {
                $counter++;
            }
        }

        return $counter;
    }

    /**
     * Counts only active shoogles.
     *
     * @param array|null $shoogleIds
     * @return int
     */
    public static function countActive(?array $shoogleIds): int
    {
        return self::countActiveInActive($shoogleIds, true);
    }

    /**
     * Counts only inactive shoogles.
     *
     * @param array|null $shoogleIds
     * @return int
     */
    public static function countInactive(?array $shoogleIds): int
    {
        return self::countActiveInActive($shoogleIds, false);
    }
}
