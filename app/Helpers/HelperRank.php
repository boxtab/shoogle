<?php

namespace App\Helpers;

use App\Constants\RankConstant;
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
     * @param int|null $rank
     * @return string|null
     */
    public static function getRankByNumber(?int $rank): ?string
    {
        return ( ( ! is_null($rank) ) && array_key_exists($rank, RankConstant::$rank) ) ? RankConstant::$rank[$rank] : null;
    }
}
