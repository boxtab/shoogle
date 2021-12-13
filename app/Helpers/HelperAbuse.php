<?php

namespace App\Helpers;

use App\Services\AbuseService;
use Exception;

/**
 * Class HelperAbuse
 * @package App\Helpers
 */
class HelperAbuse
{
    /**
     * Send a abuse.
     * @throws Exception
     */
    public static function send()
    {
        $abuseService = new AbuseService();
        $abuseService->fetchListComplaints();
        $abuseService->sendComplaint();
    }
}
