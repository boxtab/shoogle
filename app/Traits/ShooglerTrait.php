<?php

namespace App\Traits;

use App\Enums\ShooglerFilterEnum;
use App\Helpers\HelperBuddies;
use App\Models\Buddie;
use App\Models\Shoogle;
use App\Models\UserHasShoogle;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use function PHPUnit\Framework\matches;

/**
 * Trait ShooglerTrait
 * @package App\Traits
 */
trait ShooglerTrait
{
    /**
     * Returns a list of shooglers ids by shoogle ID.
     *
     * @param int $shoogleID
     * @param int|null $excludeID
     * @return array
     */
    public function getShooglersIDsByShoogleID(int $shoogleID, int $excludeID = null ): array
    {
        $shoogler = UserHasShoogle::on()
            ->where('shoogle_id', '=', $shoogleID)
            ->get('user_id')
            ->map(function ($item) {
                return $item->user_id;
            })
            ->toArray();

        if ( ! is_null($excludeID) ) {
            $shoogler = array_diff($shoogler, [$excludeID]);
        }
        return $shoogler;
    }

    /**
     * Retrieves and returns member IDs.
     *
     * @param array $shooglers
     * @return array
     */
    private function getOnlyShooglersIDs(array $shooglers): array
    {
        $response = [];
        foreach ($shooglers as $shoogler) {
            $response[] = $shoogler->id;
        }
        return $response;
    }

    /**
     * User has a friend.
     *
     * @param array|null $shooglers
     * @return array|null
     */
    public function setBaddies(?array $shooglers): ?array
    {
        if ( is_null( $shooglers ) ) {
            return $shooglers;
        }

        $response = [];
        foreach ($shooglers as $shoogler) {
            $shoogler->baddies = HelperBuddies::haveFriends($this->shoogleID, $shoogler->id);
            $response[] = $shoogler;
        }
        return $response;
    }

    /**
     * Define and set solo mode.
     *
     * @param array|null $shooglers
     * @return array|null
     */
    public function setSolo(?array $shooglers): ?array
    {
        $formation = function ($shoogler) {
            return [$shoogler['user_id'], $shoogler['solo']];
        };

        return $this->setField($shooglers, 'solo', false, $formation);
    }

    /**
     * Define and set JoinedAt.
     *
     * @param array|null $shooglers
     * @return array|null
     */
    public function setJoinedAt(?array $shooglers): ?array
    {
        $formation = function ($shoogler) {
            return [$shoogler['user_id'], date_format($shoogler['joined_at'], 'Y-m-d H:i:s')];
        };

        $default = Shoogle::on()
            ->where('id', '=', $this->shoogleID)
            ->first(['created_at'])
            ->created_at
            ->format('Y-m-d H:i:s');

        return $this->setField($shooglers, 'joined_at', $default, $formation);
    }

    /**
     * Fills in the member fields.
     *
     * @param array|null $shooglers
     * @param $fieldName
     * @param $default
     * @return array|null
     */
    private function setField(?array $shooglers, $fieldName, $default, $formation): ?array
    {
        if ( is_null( $shooglers ) ) {
            return $shooglers;
        }

        $shooglersIDs = $this->getOnlyShooglersIDs($shooglers);

        $field = UserHasShoogle::on()
            ->where('shoogle_id', '=', $this->shoogleID)
            ->whereIn('user_id', $shooglersIDs)
            ->get(['user_id', $fieldName])
            ->map($formation)
            ->toAssoc()
            ->toArray();

        $response = [];
        foreach ($shooglers as $shoogler) {
            $fieldNameCamelCase = Str::camel($fieldName);
            if ( isset($field[$shoogler->id]) ) {
                $shoogler->$fieldNameCamelCase = $field[$shoogler->id];
            } else {
                $shoogler->$fieldNameCamelCase = $default;
            }
            $response[] = $shoogler;
        }
        return $response;
    }

    /**
     * Passes users through a filter.
     *
     * @param array|null $shooglers
     * @param string|null $filter
     * @return array|null
     * @throws \ReflectionException
     */
    public function filter(?array $shooglers, ?string $filter): ?array
    {
        if ( is_null( $shooglers ) || is_null( $filter ) ) {
            return $shooglers;
        }

        if ( ! in_array( $filter, ShooglerFilterEnum::getArrayIndex()) ) {
            return $shooglers;
        }

        switch ($filter) {
            case ShooglerFilterEnum::RECENTLY_JOINED:
                $shooglerMatches = (function ($shoogler) {
//                    $carbonJoinedAt = Carbon::parse($shoogler->joinedAt);
//                    $carbonNow = Carbon::now();
//                    return ( $carbonJoinedAt->diff($carbonNow)->days < self::OUTDATED ) ? true : false;
                    return true;
                });
                break;
            case ShooglerFilterEnum::AVAILABLE:
                $shooglerMatches = (function ($shoogler) {
                    if ( $shoogler->baddies === false && $shoogler->solo === false ) {
                        return true;
                    } else {
                        return false;
                    }
                });
                break;
            case ShooglerFilterEnum::SOLO:
                $shooglerMatches = (function ($shoogler) {
                    if ( $shoogler->solo === true ) {
                        return true;
                    } else {
                        return false;
                    }
                });
                break;
            case ShooglerFilterEnum::BUDDIED:
                $shooglerMatches = (function ($shoogler) {
                    if ( $shoogler->baddies === true ) {
                        return true;
                    } else {
                        return false;
                    }
                });
                break;
        }

        $response = [];
        foreach ($shooglers as $shoogler) {
            if ( $shooglerMatches( $shoogler ) ) {
                $response[] = $shoogler;
            }
        }
        return $response;
    }

    /**
     * Sort by the date the shoogler joined the shoogle.
     *
     * @param array|null $shooglers
     * @return array|null
     */
    private function sortedRecentlyJoined(?array $shooglers): ?array
    {
        if ( is_null($shooglers) ) {
            return null;
        }

        $collection = collect($shooglers);
        return $collection->sortByDesc('joinedAt')->toArray();
    }
}
