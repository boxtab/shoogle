<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

/**
 * Class HelperInvite
 * @package App\Helpers
 */
class HelperInvite
{
    /**
     * Use invitation.
     *
     * @param int $inviteId
     * @param int $userId
     */
    public static function useInvite(int $inviteId, int $userId)
    {
        DB::table('invites')
            ->where('id', '=', $inviteId)
            ->update([
                'is_used' => 1,
                'user_id' => $userId,
            ]);
    }
}
