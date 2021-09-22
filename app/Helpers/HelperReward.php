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
            ->limit(RewardConstant::LIMIT)
            ->get();
    }
}
