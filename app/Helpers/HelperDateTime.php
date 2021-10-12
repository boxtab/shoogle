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
}
