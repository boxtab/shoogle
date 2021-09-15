<?php

namespace App\Traits;

use App\Helpers\HelperBuddies;
use App\Models\Buddie;
use App\Models\Shoogle;
use App\Models\UserHasShoogle;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
     * @return array
     */
    public function getShooglersIDsByShoogleID( int $shoogleID ): array
    {
        $owner = Shoogle::on()
            ->select('owner_id as user_id')
            ->where('id', '=', $shoogleID);

        $shoogler = UserHasShoogle::on()
            ->where('shoogle_id', '=', $shoogleID)
            ->union($owner)
            ->get('user_id')
            ->map(function ($item) {
                return $item->user_id;
            })
            ->toArray();

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
}
