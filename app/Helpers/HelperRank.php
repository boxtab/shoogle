<?php

namespace App\Helpers;

use App\Constants\RankConstant;
use App\Models\Rank;
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
     * @return string|null
     */
    public static function getRankByNumber(?int $rankId): ?string
    {
        $rankName = null;

        if ( is_null($rankId) ) {
            return null;
        }

        $rank = Rank::on()
            ->where('id', '=', $rankId)
            ->first();

        if ( is_null($rank) ) {
            return null;
        }

        $rankName = $rank->name;
        if ( is_null($rankName) ) {
            return null;
        }

        return $rankName;
    }

    /**
     * Returns the rank of the user.
     *
     * @param int|null $userId
     * @return string|null
     */
    public static function getRankByUserId(?int $userId): ?string
    {
        if ( is_null($userId) ) {
            return null;
        }

        $user = User::on()->where('id', '=', $userId)->first();
        if ( is_null($user) ) {
            return null;
        }

        $rankId = $user->rank_id;
        if ( is_null($rankId) ) {
            return null;
        }

        $rank = Rank::on()->where('id', '=', $rankId)->first();
        if ( is_null($rank) ) {
            return null;
        }

        $rankName = $rank->name;
        if ( is_null($rankName) ) {
            return null;
        }

        return $rankName;
    }

    /**
     * Increases rank.
     *
     * @param int|null $userId
     */
    public static function increaseRank(?int $userId)
    {
        if ( is_null($userId) ) {
            return;
        }

        $rankService = new RankService($userId);
        if ( ! $rankService->isUserFound() ) {
            return;
        }
    }
}
