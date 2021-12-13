<?php

namespace App\Services;

use App\Helpers\HelperConfigCron;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Class AbuseService
 * @package App\Services
 */
class AbuseService
{
    /**
     * @var array list of complaints.
     */
    private $listComplaints;

    /**
     * @throws Exception
     */
    public function fetchListComplaints()
    {
        try {
            $this->listComplaints = HelperConfigCron::getMessagesWithFlag();
        } catch (Exception $e) {
            Log::info('===================================================================');
            Log::info($e->getMessage());
            Log::info('===================================================================');
        }

    }

    /**
     * Send a complaint.
     */
    public function sendComplaint()
    {
        Log::info('sendComplaint');
        Log::info($this->listComplaints);
    }
}
