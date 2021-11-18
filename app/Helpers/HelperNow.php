<?php

namespace App\Helpers;

use App\Models\DateNow;
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
     * Retrieve current date time from database.
     *
     * @return false|mixed|string
     */
    public static function fetchDateTime()
    {
        $dateNow = DateNow::on()->first();

        if ( ! is_null( $dateNow ) ) {
            $currentDateTime = $dateNow->date_time_now;
        } else {
            $currentDateTime = date(Carbon::now());
        }
        return $currentDateTime;
    }

    /**
     * Get timestamp.
     *
     * @return int
     */
    public static function getTimestamp(): int
    {
//        return strtotime(self::$currentDateTime);
        return Carbon::now()->timestamp;
//        return strtotime( self::fetchDateTime() );
    }

    public static function getCarbon(): Carbon
    {
//        return Carbon::createFromFormat('Y-m-d H:i:s', self::$currentDateTime);
        return Carbon::now();
//        return Carbon::createFromFormat('Y-m-d H:i:s', self::fetchDateTime());
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
//        return date('Y-m-d', strtotime( self::fetchDateTime() ));
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
//        return date('Y-m-d H:i:s', strtotime( self::fetchDateTime()) );
    }
}
