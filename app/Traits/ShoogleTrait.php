<?php


namespace App\Traits;

use App\Models\Buddie;
use App\Models\Shoogle;
use App\Models\UserHasShoogle;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

    /**
     * Set buddies for the current authenticated user.
     *
     * @param array|null $shoogles
     * @return array|null
     */
    public function setBuddy( ?array $shoogles ): ?array
    {
        if ( is_null( $shoogles ) ) {
            return $shoogles;
        }

        $authenticatedUserID = Auth::id();
        $buddies = Buddie::on()
            ->whereIn('shoogle_id', $this->getShoogleIDsByUserId($authenticatedUserID))
            ->whereNull('disconnected_at')
            ->where(function ($query) use ($authenticatedUserID) {
                $query->where('user1_id', '=', $authenticatedUserID)
                    ->orWhere('user2_id', '=', $authenticatedUserID);

            })
            ->get(['shoogle_id', 'user1_id', 'user2_id'])
            ->map(function ($buddy) use ($authenticatedUserID) {
                $buddyID = ( $buddy['user1_id'] === $authenticatedUserID ) ? $buddy['user2_id'] : $buddy['user1_id'];
                $buddyName = User::on()->where('id', '=', $buddyID)->first()->full_name;
                return [$buddy['shoogle_id'], $buddyName];
            })
            ->toAssoc()
            ->toArray();


        if ( is_null( $buddies ) ) {
            return $shoogles;
        }

        $response = [];
        foreach ($shoogles as $shoogle) {
            if ( isset($buddies[$shoogle->id]) ) {
                $shoogle->buddyName = $buddies[$shoogle->id];
            }
            $response[] = $shoogle;
        }

        return $response;
    }

    /**
     * Calculates the number of participants in shoogle.
     *
     * @param array|null $shoogles
     * @return array|null
     */
    public function setShooglersCount( ?array $shoogles ): ?array
    {
        if ( is_null( $shoogles ) ) {
            return $shoogles;
        }

        $authenticatedUserID = Auth::id();
        $shooglersCount = UserHasShoogle::on()
            ->select('shoogle_id', DB::raw('count(user_id) as total'))
            ->where('user_id', '<>', $authenticatedUserID)
            ->whereIn('shoogle_id', $this->getShoogleIDsByUserId($authenticatedUserID))
            ->groupBy('shoogle_id')
            ->get(['shoogle_id', 'total'])
            ->map(function ($shoogle) {
                return [$shoogle['shoogle_id'], $shoogle['total']];
            })
            ->toAssoc()
            ->toArray();

        $response = [];
        foreach ($shoogles as $shoogle) {
            if ( isset($shooglersCount[$shoogle->id]) ) {
                $shoogle->shooglersCount = ($shooglersCount[$shoogle->id] + 1);
            } else {
                $shoogle->shooglersCount = 1;
            }
            $response[] = $shoogle;
        }

        return $response;
    }

    /**
     * Get count of members who have a "friend" in the buddies table.
     *
     * @param array|null $shoogles
     * @return array|null
     */
    public function setBuddiesCount( ?array $shoogles ): ?array
    {
        if ( is_null( $shoogles ) ) {
            return $shoogles;
        }

        $authenticatedUserID = Auth::id();
        $owners = Shoogle::on()
            ->select('shoogles.id as shoogle_id', 'shoogles.owner_id as user_id')
            ->where('owner_id', '=', $authenticatedUserID);

        $members = UserHasShoogle::on()
            ->select('shoogle_id', 'user_id')
            ->whereIn('shoogle_id', $this->getShoogleIDsByUserId($authenticatedUserID))
            ->union($owners);

        $buddies = $members
            ->whereExists(function($query)
            {
                $query->select(DB::raw(1))
                    ->from('buddies')
                    ->whereRaw('(buddies.shoogle_id = shoogle_id) AND (buddies.user1_id = user_id OR buddies.user2_id = user_id) ');
            })
            ->get()
            ->toArray();

        $buddiesCount = collect($buddies)->groupBy('shoogle_id')->map(function ($row) {
            return $row->count('user_id');
        })->toArray();

        $response = [];
        foreach ($shoogles as $shoogle) {
            if ( isset($buddiesCount[$shoogle->id]) ) {
                $shoogle->buddiesCount = $buddiesCount[$shoogle->id];
            } else {
                $shoogle->buddiesCount = 0;
            }
            $response[] = $shoogle;
        }

        return $response;
    }
}
