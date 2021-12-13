<?php

namespace App\Services;

use App\Helpers\HelperConfigCron;
use App\User;
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
     * @var array list of abuses.
     */
    private $listAbuses;

    /**
     * User ID prefix.
     */
    const PREFIX_USER_ID = 'user';

    /**
     * Converting user id from third party system to database id.
     *
     * @param string|null $textUserId
     * @return int|null
     */
    private function getUserId(?string $textUserId): ?int
    {
        if ( is_null($textUserId) ) {
            return null;
        }

        $numberUserId = (int)substr($textUserId, strlen(self::PREFIX_USER_ID));

        $user = User::on()->where('id', '=', $numberUserId)->first();

        if ( is_null($user) ) {
            return null;
        }

        return $user->id;
    }

    /**
     * @throws Exception
     */
    public function fetchListComplaints()
    {
        try {
            $this->listComplaints = HelperConfigCron::getMessagesWithFlag();
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
    }

    /**
     * Processing complaints from the service https://getstream.io
     */
    public function handlingAbuses()
    {
        foreach ($this->listComplaints as $complaint) {
            $this->listAbuses[] = [
                'date_abuse'        => $complaint['user']['created_at'],
                'from_user_id'      => $this->getUserId( $complaint['user']['id'] ),
                'to_user_id'        => $this->getUserId( $complaint['message']['user']['id'] ),
                'company_admin_id'  => null,
                'message_id'        => $complaint['message']['id'],
            ];
        }
    }

    /**
     * Send a complaint.
     */
    public function sendComplaint()
    {
        Log::info('sendComplaint');
        Log::info($this->listAbuses);
    }
}
