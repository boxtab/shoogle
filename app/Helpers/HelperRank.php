<?php

namespace App\Helpers;

use App\Constants\RankConstant;
use App\Models\Rank;
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

//    public static function getRankByUserId(?int $)
}
