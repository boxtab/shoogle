<?php


namespace App\Traits;

use App\Models\Shoogle;
use App\Models\UserHasShoogle;
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

    public function setSoloMode( ?array $shoogles ): ?array
    {
        if ( is_null( $shoogles ) ) {
            return null;
        }
        $response = [];

        foreach ($shoogles as $shoogle) {
            $shoogle->title = $shoogle->title . ' - mytest';
            $response[] = $shoogle;
        }

//        Log::info( ((array)$shoogles[1])['title'] );

//        foreach ( $shoogles as $shoogle ) {
//            $rowShoogle =& $shoogle;

//            (array)$rowShoogle['solo'] = 1;

//            Log::info($rowShoogle);
//            Log::info($rowShoogle['id']);
//        }

//        Log::info($shoogles);
        return $response;
    }
}
