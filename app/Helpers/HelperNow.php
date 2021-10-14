<?php

namespace App\Helpers;

use Carbon\Carbon;

/**
 * Class HelperNow
 *
 *                                                      BUILT FOR TESTING
 *
 * @package App\Helpers
 */
class HelperNow
{
    private static $currentDateTime = '2021-10-19 11:17:00';

    /**
     * Get timestamp.
     *
     * @return int
     */
    public static function getTimestamp(): int
    {
//        return strtotime(self::$currentDateTime);
        return Carbon::now()->timestamp;
    }

    public static function getCarbon(): Carbon
    {
//        return Carbon::createFromFormat('Y-m-d H:i:s', self::$currentDateTime);
        return Carbon::now();
    }

    /**
     * Get date/
     *
     * @return string
     */
    public static function getDate(): string
    {
//        return date('Y-m-d', strtotime(self::$currentDateTime));
        return Carbon::now()->toDateString();
    }

    /**
     * Get date time.
     *
     * @return string
     */
    public static function getDateTime(): string
    {
//        return date('Y-m-d H:i:s', strtotime(self::$currentDateTime));
        return Carbon::now()->toDateTimeString();
    }
}
