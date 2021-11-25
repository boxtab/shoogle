<?php

namespace App\Helpers;

use App\Constants\RewardConstant;
use App\Models\Reward;
use App\Models\UserHasReward;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

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

    /**
     * Reward information.
     *
     * @param int|null $notificationId
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public static function getAwarded(?int $notificationId)
    {
        if ( is_null($notificationId) ) {
            return null;
        }

        return UserHasReward::on()
            ->leftJoin('users', 'users.id', '=', 'user_has_reward.given_by_user_id')
            ->leftJoin('rewards', 'rewards.id', '=', 'user_has_reward.reward_id')
            ->select([
                'users.profile_image as profileImage',
                'users.first_name as firstName',
                'users.last_name as lastName',
                'rewards.name as rewardName',
                'rewards.icon as rewardIcon',
            ])
            ->where('notification_id', '=', $notificationId)
            ->first();
    }

    /**
     * The reward exists.
     *
     * @param int|null $rewardId
     * @throws Exception
     */
    public static function isReward(?int $rewardId)
    {
        if ( is_null($rewardId) ) {
            throw new Exception('Award ID not shared.', Response::HTTP_NOT_FOUND);
        }

        $reward = Reward::on()->where('id', '=', $rewardId)->first();
        if ( is_null($reward) ) {
            throw new Exception('Reward not found.', Response::HTTP_NOT_FOUND);
        }
    }
}
