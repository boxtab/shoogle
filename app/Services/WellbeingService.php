<?php

namespace App\Services;

use App\Constants\NotificationsTypeConstant;
use App\Constants\NotificationTextConstant;
use App\Constants\RoleConstant;
use App\Helpers\HelperNotifications;
use App\Models\Role;
use App\User;
use Illuminate\Support\Facades\Log;

/**
 * Class WellbeingService
 * @package App\Services
 */
class WellbeingService
{
    /**
     * Get a lot of user IDs to send notifications.
     *
     * @return array
     */
    private function getUsers(): array
    {
        $role = Role::on()
            ->where('name', '=', RoleConstant::SUPER_ADMIN)
            ->first();

        if ( is_null($role) ) {
            return [];
        }

        $userRoleId = $role->id;

        return User::on()
            ->select('users.id as id')
            ->whereHas('manyRole', function ($query) use($userRoleId) {
                $query->where('model_has_roles.role_id', '!=', $userRoleId);
            })
            ->get()
            ->map(function ($item) {
                return $item->id;
            })
            ->toArray();
    }


//    private function isConditionSending(int $userId): bool
//    {
//        null;
//    }

    /**
     * Launching a notification mailing request to set wellbeing.
     *
     * @return int
     * @throws \GetStream\StreamChat\StreamException
     */
    public function run(): int
    {
        $userIds = $this->getUsers();

        foreach ($userIds as $userId) {

            $helper = new HelperNotifications();
            $helper->sendNotificationToUser(
                $userId,
                NotificationsTypeConstant::WELLBEING_REMIDER_ID,
                NotificationTextConstant::WELLBEING
            );

        }

        return count($userIds);
    }
}
