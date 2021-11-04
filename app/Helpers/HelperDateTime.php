<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

/**
 * Class HelperDateTime
 * @package App\Helpers
 */
class HelperDateTime
{
    /**
     * Returns only time from a variable with date time.
     *
     * @param string $datetime
     * @return string
     */
    public static function getTime(string $datetime): string
    {
        return date('H:i:s', strtotime($datetime));
    }

    /**
     * Get yesterday.
     *
     * @param string $datetime
     * @return string
     */
    public static function getYesterday(string $datetime): string
    {
        return date('Y-m-d H:i:s', strtotime('-1 day', strtotime($datetime)));
    }

    /**
     * Add one year to the incoming date.
     *
     * @param string $datetime
     * @return string
     */
    public static function getPlusOneYear(string $datetime): string
    {
        return date('Y-m-d H:i:s', strtotime('+1 year', strtotime($datetime)));
    }

    /**
     * Date from must be less than date to.
     *
     * @param string|null $dateFrom
     * @param string|null $dateTo
     * @return bool
     */
    public static function checkDateFromLessDateTo(?string $dateFrom, ?string $dateTo): bool
    {
        if ( ! is_null($dateFrom) && ! is_null($dateTo) ) {
            if ( strtotime($dateFrom) > strtotime($dateTo) ) {
                return false;
            }
        }
        return true;
    }

    /**
     * Pair check.
     *
     * @param string|null $dateFrom
     * @param string|null $dateTo
     * @return bool
     */
    public static function checkDatePair(?string $dateFrom, ?string $dateTo): bool
    {
        if ( is_null($dateFrom) && ! is_null($dateTo) ) {
            return true;
        }

        if ( ! is_null($dateFrom) && is_null($dateTo) ) {
            return true;
        }

        return false;
    }
}
