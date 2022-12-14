<?php

namespace App\Traits;

use App\Helpers\HelperBuddies;
use App\Helpers\HelperMember;
use App\Helpers\HelperShoogle;
use App\Helpers\HelperShoogleList;
use App\Models\Buddie;
use App\Models\Shoogle;
use App\Models\UserHasShoogle;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PHPUnit\TextUI\Help;

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

        $listIDsUserHasShoogle = UserHasShoogle::on()
            ->leftJoin('shoogles', 'user_has_shoogle.shoogle_id', '=', 'shoogles.id')
            ->where('shoogles.active', '=', 1)
            ->where('user_has_shoogle.user_id', '=', $userID)
            ->get('user_has_shoogle.shoogle_id')
            ->map(function ($item) {
                return $item->shoogle_id;
            })
            ->toArray();

        return array_unique( $listIDsUserHasShoogle );
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
                $buddy = User::on()->where('id', '=', $buddyID)->first();
                $buddyName = ( ! is_null($buddy) ) ? $buddy->full_name : null;
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

        /*
        $authenticatedUserID = Auth::id();

        $shooglersCount = UserHasShoogle::on()
            ->select('shoogle_id', DB::raw('count(user_id) as total'))
            ->where('user_id', '<>', $authenticatedUserID)
//            ->whereIn('shoogle_id', $this->getShoogleIDsByUserId($authenticatedUserID))
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
        */

        $response = [];
        foreach ($shoogles as $shoogle) {
            $shoogle->shooglersCount = HelperShoogle::getShooglersCount($shoogle->id);
            $response[] = $shoogle;
        }

        return $response;
    }

    /**
     * Calculating the number of members who have banned friend requests.
     *
     * @param array|null $shoogles
     * @return array|null
     */
    public function setSolosCount( ?array  $shoogles ): ?array
    {
        if ( is_null( $shoogles ) ) {
            return $shoogles;
        }

        $authenticatedUserID = Auth::id();
        $solosCount = UserHasShoogle::on()
            ->select('shoogle_id', DB::raw('count(user_id) as total'))
            ->whereIn('shoogle_id', $this->getShoogleIDsByUserId($authenticatedUserID))
            ->where('solo', '=', 1)
            ->groupBy('shoogle_id')
            ->get(['shoogle_id', 'total'])
            ->map(function ($shoogle) {
                return [$shoogle['shoogle_id'], $shoogle['total']];
            })
            ->toAssoc()
            ->toArray();

        $response = [];
        foreach ($shoogles as $shoogle) {
            if ( isset($solosCount[$shoogle->id]) ) {
                $shoogle->solosCount = $solosCount[$shoogle->id];
            } else {
                $shoogle->solosCount = 0;
            }
            $response[] = $shoogle;
        }
        return $response;
    }

    /**
     * Is this user a member of a specific shoogle.
     *
     * @param array|null $shoogles
     * @return array|null
     */
    public function setJoined( ?array  $shoogles ): ?array
    {
        if ( is_null( $shoogles ) ) {
            return $shoogles;
        }

        $authenticatedUserID = Auth::id();
        $response = [];
        foreach ($shoogles as $shoogle) {
            $shoogle->joined = HelperMember::isMember($shoogle->id, $authenticatedUserID);
            $response[] = $shoogle;
        }
        return $response;
    }

    /**
     * Is the current user a shoogle creator.
     *
     * @param array|null $shoogles
     * @return array|null
     */
    public function setOwner(?array $shoogles): ?array
    {
        if ( is_null( $shoogles ) ) {
            return $shoogles;
        }

        $response = [];
        foreach ($shoogles as $shoogle) {
            $shoogle->owner = HelperShoogleList::isOwner(Auth::id(), $shoogle->id);
            $response[] = $shoogle;
        }
        return $response;
    }

    /**
     * Set a chat name with a buddy.
     *
     * @param array|null $shoogles
     * @return array|null
     */
    public function setChatNameWithBuddy(?array $shoogles): ?array
    {
        if ( is_null( $shoogles ) ) {
            return $shoogles;
        }

        $response = [];
        foreach ($shoogles as $shoogle) {
            $chatNameWithBuddy = null;
            $buddy = HelperBuddies::getBuddy($shoogle->id, Auth::id());
            if ( ! is_null($buddy) ) {
                $chatNameWithBuddy = $buddy->chat_id;
            }
            $shoogle->chatNameWithBuddy = $chatNameWithBuddy;
            $response[] = $shoogle;
        }

        return $response;
    }
}
