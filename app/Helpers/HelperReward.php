<?php

namespace App\Helpers;

use App\Constants\RewardConstant;
use App\Models\UserHasReward;

/**
 * Class HelperReward
 * @package App\Helpers
 */
class HelperReward
{
    /**
     * Retrieve a list of user rewards.
     *
     * @param int|null $userID
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function getReward(?int $userID)
    {
        return UserHasReward::on()
            ->where('user_id', $userID)
            ->orderBy('created_at', 'desc')
//            ->limit(RewardConstant::LIMIT)
            ->get();
    }

    /**
     * Full url to the user reward.
     *
     * @param string|null $fileName
     * @return string|null
     */
    public static function getURLReward( ?string $fileName ): ?string
    {
        return  ( ! is_null($fileName) ) ? url(RewardConstant::PATH) . '/' . $fileName : null;
    }

    public static function getAwarded(?int $userHasRewardId)
    {
        if ( is_null($userHasRewardId) ) {
            return null;
        }

        return UserHasReward::on()->where('id', '=', $userHasRewardId)->first();
    }
}
