<?php

namespace App\Helpers;

use App\Services\RankService;
use Illuminate\Support\Facades\Log;

/**
 * Class HelperRankServiceClient
 * @package App\Helpers
 */
class HelperRankServiceClient
{
    /**
     * Increases rank.
     *
     * @param int|null $userId
     * @throws \GetStream\StreamChat\StreamException
     */
    public static function assignRank(?int $userId)
    {
        if ( is_null($userId) ) {
            return;
        }

        $rankService = new RankService($userId);
        if ( ! $rankService->isUserFound() ) {
            return;
        }

        $rankService->fetchCountShoogleViews();
        $rankService->fetchCountWellbeingScores();
        $rankService->fetchCountReward();
        $rankService->fetchOldRankId();
        $rankService->calculatingNewRankId();

        if ( $rankService->isGiveRank() ) {
            $rankService->assignRank();
            $rankService->sendNotification();
        }
    }
}

