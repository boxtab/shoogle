<?php

namespace App\Services;

use App\Helpers\HelperCompany;
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
     * Get company admin id of user.
     *
     * @param string|null $textUserId
     * @return int|null
     */
    public function getAdminId(?string $textUserId): ?int
    {
        if ( is_null( $textUserId ) ) {
            return null;
        }

        $userId = $this->getUserId($textUserId);
        if ( is_null( $userId ) ) {
            return null;
        }

        $companyId = HelperCompany::getCompanyIdByUserId($userId);
        if ( is_null( $companyId ) ) {
            return null;
        }

        return HelperCompany::getAdminIdByCompanyId($companyId);
    }

    /**
     * Retrieve remote complaint list.
     *
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
                'company_admin_id'  => $this->getAdminId( $complaint['message']['user']['id'] ),
                'message_id'        => $complaint['message']['id'],
            ];
        }
    }

    /**
     * Test complaint.
     */
    public function addAbuseTest()
    {
        $this->listAbuses[] = [
            'date_abuse'        => '2021-10-18T14:31:03.859809Z',
            'from_user_id'      => 125,
            'to_user_id'        => 126,
            'company_admin_id'  => 124,
            'message_id'        => 'test_message_id',
        ];
    }

    /**
     * Checking the completeness of fields.
     */
    public function checkFillingFields()
    {
        $listAbusesVerified = [];

        foreach ($this->listAbuses as $abuse) {
            if (
                ! is_null($abuse['date_abuse']) &&
                ! is_null($abuse['from_user_id']) &&
                ! is_null($abuse['to_user_id']) &&
                ! is_null($abuse['company_admin_id'])
            ) {
                $listAbusesVerified[] = $abuse;
            }
        }

        $this->listAbuses = $listAbusesVerified;
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
