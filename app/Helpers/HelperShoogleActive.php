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
     * Returns a list of shoogles with the active / inactive flag.
     *
     * @param array|null $shoogleIds
     * @return array|null
     */
    public static function getList(?array $shoogleIds): ?array
    {
        if ( empty($shoogleIds) ) {
            return null;
        }

        $listShoogleIDsActive = [];
        foreach ($shoogleIds as $shoogleId) {
            $listShoogleIDsActive[$shoogleId] = (int)self::isActive($shoogleId);
        }

        return $listShoogleIDsActive;
    }

    /**
     * Counting the number of active shoogle.
     *
     * @param array|null $listShooglesActiveInactive
     * @return int
     */
    public static function getCountActive(?array $listShooglesActiveInactive): int
    {
        if ( is_null($listShooglesActiveInactive) ) {
            return 0;
        }

        $counterActive = 0;

        foreach ($listShooglesActiveInactive as $shooglesActiveInactive) {
            if ( $shooglesActiveInactive === 1 ) {
                $counterActive++;
            }
        }

        return $counterActive;
    }

    /**
     * Counting the number of inactive shoogle.
     *
     * @param array|null $listShooglesActiveInactive
     * @return int
     */
    public static function getCountInactive(?array $listShooglesActiveInactive): int
    {
        if ( is_null($listShooglesActiveInactive) ) {
            return 0;
        }

        $counterInactive = 0;

        foreach ($listShooglesActiveInactive as $shooglesActiveInactive) {
            if ( $shooglesActiveInactive === 0 ) {
                $counterInactive++;
            }
        }

        return $counterInactive;
    }
}
