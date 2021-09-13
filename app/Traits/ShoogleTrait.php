<?php


namespace App\Traits;

use App\Models\Shoogle;
use App\Models\UserHasShoogle;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Trait ShoogleTrait
 * @package App\Traits
 */
trait ShoogleTrait
{
    /**
     * Get an array of unique shoofles IDs by user ID.
     *
     * @param int|null $userID
     * @return array
     */
    public function getShoogleIDsByUserId( ?int $userID ): array
    {
        if ( is_null( $userID ) ) {
            return [];
        }

        $listIDsShoogles = Shoogle::on()
            ->where('owner_id', '=', $userID)
            ->get('id')
            ->map(function ($item) {
                return $item->id;
            })
            ->toArray();

        $listIDsUserHasShoogle = UserHasShoogle::on()
            ->where('user_id', '=', $userID)
            ->get('shoogle_id')
            ->map(function ($item) {
                return $item->shoogle_id;
            })
            ->toArray();

        $shoogleIDs = array_merge( $listIDsShoogles, $listIDsUserHasShoogle );
        $uniqueShoogleIDs = array_unique( $shoogleIDs );

        return $uniqueShoogleIDs;
    }

    /**
     * Sets solo mode.
     *
     * @param array|null $shoogles
     * @return array|null
     */
    public function setSoloMode( ?array $shoogles ): ?array
    {
        if ( is_null( $shoogles ) ) {
            return $shoogles;
        }

        $solo = UserHasShoogle::on()
            ->where('user_id', '=', Auth::id())
            ->get(['shoogle_id', 'solo'])
            ->map(function ($shoogle) {
                return [$shoogle['shoogle_id'], $shoogle['solo']];
            })
            ->toAssoc()
            ->toArray();

        $response = [];
        foreach ($shoogles as $shoogle) {
            if ( isset($solo[$shoogle->id]) && $solo[$shoogle->id] === true ) {
                $shoogle->solo = true;
            } else {
                $shoogle->solo = false;
            }
            $response[] = $shoogle;
        }

        return $response;
    }

    public function setBuddy( ?array $shoogles ): ?array
    {
        if ( is_null( $shoogles ) ) {
            return null;
        }



        $response = [];
        foreach ($shoogles as $shoogle) {
            $response[] = $shoogle;
        }

        return $response;
    }
}
