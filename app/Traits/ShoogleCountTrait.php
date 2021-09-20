<?php

namespace App\Traits;

use App\Helpers\HelperShoogleStatistic;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Trait ShoogleCountTrait
 * @package App\Traits
 */
trait ShoogleCountTrait
{
    /**
     * Universal method.
     * Calculates the number of participants in shoogle.
     *
     * @param array|null $shoogles
     * @return array|null
     */
    public function setGeneralShooglersCount( ?array  $shoogles ): ?array
    {
        if ( is_null( $shoogles ) ) {
            return $shoogles;
        }

        $response = [];
        foreach ($shoogles as $shoogle) {
            $shoogle->shooglersCount = HelperShoogleStatistic::getShooglersCount($shoogle->id);
            $response[] = $shoogle;
        }
        return $response;
    }

    /**
     * Universal method.
     * Get count of members who have a "friend" in the buddies table.
     *
     * @param array|null $shoogles
     * @return array|null
     */
    public function setGeneralBuddiesCount( ?array  $shoogles ): ?array
    {
        if ( is_null( $shoogles ) ) {
            return $shoogles;
        }

        $response = [];
        foreach ($shoogles as $shoogle) {
            $shoogle->buddiesCount = HelperShoogleStatistic::getBuddiesCount($shoogle->id);
            $response[] = $shoogle;
        }
        return $response;
    }

    /**
     * Universal method.
     * Calculating the number of members who have banned friend requests.
     *
     * @param array|null $shoogles
     * @return array|null
     */
    public function setGeneralSolosCount( ?array  $shoogles ): ?array
    {
        if ( is_null( $shoogles ) ) {
            return $shoogles;
        }

        $response = [];
        foreach ($shoogles as $shoogle) {
            $shoogle->solosCount = HelperShoogleStatistic::getSolosCount($shoogle->id);
            $response[] = $shoogle;
        }
        return $response;
    }

}
