<?php

namespace App\Services;

use App\Constants\NotificationsTypeConstant;
use App\Constants\RankConstant;
use App\Constants\RankMeritConstant as Merit;
use App\Helpers\HelperNotifications;
use App\Helpers\HelperRank;
use App\Models\ShoogleViews;
use App\Models\UserHasReward;
use App\Models\WellbeingScores;
use App\User;
use Illuminate\Support\Facades\Log;

/**
 * Class RankService
 * @package App\Services
 */
class RankService
{
    private $userId = null;

    private $countShoogleViews = 0;

    private $countWellbeingScores = 0;

    private $countReward = 0;

    private $oldRankId;

    private $newRankId;

    /**
     * RankService constructor.
     * @param int|null $userId
     */
    public function __construct(?int $userId)
    {
        $this->userId = $userId;
    }

    /**
     * User found or not found.
     *
     * @return bool
     */
    public function isUserFound(): bool
    {
        if ( is_null($this->userId) ) {
            return false;
        }

        $user = User::on()
            ->where('id', '=', $this->userId)
            ->first();

        if ( is_null($user) ) {
            return false;
        }

        return true;
    }

    /**
     * Extract sum of views of shoogle.
     */
    public function fetchCountShoogleViews()
    {
        $this->countShoogleViews = ShoogleViews::on()
            ->where('user_id', '=', $this->userId)
            ->sum('views');
    }

    /**
     * Extract the number of wellbeing.
     */
    public function fetchCountWellbeingScores()
    {
        $this->countWellbeingScores = WellbeingScores::on()
            ->where('user_id', '=', $this->userId)
            ->count();
    }

    /**
     * Extract number of awards.
     */
    public function fetchCountReward()
    {
        $this->countReward = UserHasReward::on()
            ->where('user_id', '=', $this->userId)
            ->count();
    }

    /**
     * Retrieve old rank.
     */
    public function fetchOldRankId()
    {
        $this->oldRankId = HelperRank::getRankIdByUserId($this->userId);
    }

    /**
     * Calculating a new rank.
     */
    public function calculatingNewRankId()
    {
        $shoogle = $this->countShoogleViews;
        $wellbeing = $this->countWellbeingScores;
        $reward = $this->countReward;

        if ( $shoogle >= Merit::EXPERT['shoogle'] && $wellbeing >= Merit::EXPERT['wellbeing'] && $reward >= Merit::EXPERT['reward'] ) {

            $newRankId = RankConstant::EXPERT_ID;

        } elseif ( $shoogle >= Merit::EXPERIENCED['shoogle'] && $wellbeing >= Merit::EXPERIENCED['wellbeing'] && $reward >= Merit::EXPERIENCED['reward'] ) {

            $newRankId = RankConstant::EXPERIENCED_ID;

        } elseif ( $shoogle >= Merit::INTERMEDIATE['shoogle'] && $wellbeing >= Merit::INTERMEDIATE['wellbeing'] && $reward >= Merit::INTERMEDIATE['reward'] ) {

            $newRankId = RankConstant::INTERMEDIATE_ID;

        } elseif ( $shoogle >= Merit::ROOKIE['shoogle'] && $wellbeing >= Merit::ROOKIE['wellbeing'] && $reward >= Merit::ROOKIE['reward'] ) {

            $newRankId = RankConstant::ROOKIE_ID;

        } else {
            $newRankId = RankConstant::NEWBIE_ID;
        }

        $this->newRankId = $newRankId;
    }

    /**
     * Assign a rank?
     *
     * @return bool
     */
    public function isGiveRank(): bool
    {
        return ( $this->oldRankId < $this->newRankId ) ? true : false;
    }

    /**
     * Assign a new rank.
     */
    public function assignRank()
    {
        User::on()
            ->where('id', '=', $this->userId)
            ->update([
                'rank_id' => $this->newRankId,
            ]);
    }

    /**
     * Send message about new rank.
     *
     * @throws \GetStream\StreamChat\StreamException
     */
    public function sendNotification()
    {
        $newRankName = HelperRank::getRankNameByRankId( $this->newRankId );

        $helperNotification = new HelperNotifications();
        $helperNotification->sendNotificationToUser(
            $this->userId,
            NotificationsTypeConstant::RANK_ASSIGN_ID,
            "Congratulations! Your new rank in shoogle is $newRankName"
        );
    }
}
