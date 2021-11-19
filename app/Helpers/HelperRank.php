<?php

namespace App\Helpers;

use App\Constants\NotificationsTypeConstant;
use App\Constants\RankConstant;
use App\Models\NotificationToUser;
use App\Models\Rank;
use App\Scopes\NotificationToUserScope;
use App\Services\RankService;
use App\User;
use Illuminate\Support\Facades\Log;

/**
 * Class HelperRank
 * @package App\Helpers
 */
class HelperRank
{
    /**
     * By rank number, returns its text value.
     *
     * @param int|null $rankId
     * @return string
     */
    public static function getRankNameByRankId(?int $rankId): string
    {
        $rankName = RankConstant::NEWBIE_NAME;

        if ( is_null($rankId) ) {
            return $rankName;
        }

        $rank = Rank::on()
            ->where('id', '=', $rankId)
            ->first();

        if ( is_null($rank) ) {
            return $rankName;
        }

        $rankName = $rank->name;
        if ( is_null($rankName) ) {
            return RankConstant::NEWBIE_NAME;
        }

        return $rankName;
    }

    /**
     * By the name of the rank, we return its identifier.
     *
     * @param string|null $rankName
     * @return string
     */
    public static function getRankIdByRankName(?string $rankName): string
    {
        $rankId = RankConstant::NEWBIE_ID;

        if ( is_null($rankName) ) {
            return  $rankId;
        }

        $rank = Rank::on()
            ->where('name', '=', $rankName)
            ->first();

        if ( is_null($rank) ) {
            return $rankId;
        }

        $rankId = $rank->id;
        if ( is_null($rankId) ) {
            return RankConstant::NEWBIE_ID;
        }

        return $rankId;
    }

    /**
     * Returns the rank name of the user.
     *
     * @param int|null $userId
     * @return string
     */
    public static function getRankNameByUserId(?int $userId): string
    {
        if ( is_null($userId) ) {
            return RankConstant::NEWBIE_NAME;
        }

        $user = User::on()->where('id', '=', $userId)->first();
        if ( is_null($user) ) {
            return RankConstant::NEWBIE_NAME;
        }

        $rankId = $user->rank_id;
        if ( is_null($rankId) ) {
            return RankConstant::NEWBIE_NAME;
        }

        $rank = Rank::on()->where('id', '=', $rankId)->first();
        if ( is_null($rank) ) {
            return RankConstant::NEWBIE_NAME;
        }

        $rankName = $rank->name;
        if ( is_null($rankName) ) {
            return RankConstant::NEWBIE_NAME;
        }

        return $rankName;
    }

    /**
     * Returns the rank id of the user.
     *
     * @param int|null $userId
     * @return string
     */
    public static function getRankIdByUserId(?int $userId): string
    {
        $rankId = RankConstant::NEWBIE_ID;
        $rankName = self::getRankNameByUserId($userId);

        $rank = Rank::on()
            ->where('name', '=', $rankName)
            ->first();

        if ( is_null($rank) ) {
            return $rankId;
        }

        return $rank->id;
    }

    /**
     * Rank up notification.
     *
     * @param int|null $notificationId
     * @return array|null
     */
    public static function getNotification(?int $notificationId):  ?array
    {
        if ( is_null( $notificationId ) ) {
            return null;
        }

        $notificationToUser = NotificationToUser::on()
            ->withoutGlobalScope(NotificationToUserScope::class)
            ->where('id', '=', $notificationId)
            ->where('type_id', '=', NotificationsTypeConstant::RANK_ASSIGN_ID)
            ->first();

        if ( is_null($notificationToUser) ) {
            return null;
        }

        return [
            'title' => 'You have earned a new rank',
            'message' => $notificationToUser->notification,
        ];
    }
}
