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
}
