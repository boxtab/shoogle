<?php

namespace App\Helpers;

use App\Models\Shoogle;
use App\Models\UserHasShoogle;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class HelperShoogleProfile
 * @package App\Helpers
 */
class HelperShoogleProfile
{
    /**
     * @var array shoogles.
     */
    private $shoogles = [];

    /**
     * @var int Shoogles count.
     */
    private $shooglesCount = 0;

    /**
     * @var int Active shoogles count.
     */
    private $activeShooglesCount = 0;

    /**
     * @var int Inactive Shoogles count.
     */
    private $inactiveShooglesCount = 0;

    /**
     * Get shoogles.
     *
     * @return array
     */
    public function getShoogles()
    {
        return $this->shoogles;
    }

    /**
     * Get shoogles count.
     *
     * @return int
     */
    public function getShooglesCount()
    {
        return $this->shooglesCount;
    }

    /**
     * Get active shoogles count.
     *
     * @return int
     */
    public function getActiveShooglesCount()
    {
        return $this->activeShooglesCount;
    }

    /**
     * Get inactive shoogles count.
     *
     * @return int
     */
    public function getInactiveShooglesCount()
    {
        return $this->inactiveShooglesCount;
    }

    /**
     * HelperShoogleProfile constructor.
     *
     * @param int|null $userID
     */
    public function __construct(?int $userID)
    {
        if ( is_null( $userID ) ) {
            return [];
        }

        $shooglesIDs = HelperShoogle::getShooglesIDsByUserID($userID);

        $this->activeShooglesCount = Shoogle::on()
            ->whereIn('id', $shooglesIDs)
            ->where('active', '=', 1)
            ->count();

        $this->inactiveShooglesCount = Shoogle::on()
            ->whereIn('id', $shooglesIDs)
            ->where('active', '=', 0)
            ->count();;

        $shoogles = Shoogle::on()
            ->select(DB::raw("
                shoogles.id as id,
                shoogles.title as title,
                shoogles.cover_image as coverImage,
                if((exists (
                    select * from buddies as b
                    where b.shoogle_id = shoogles.id
                      and (
                        b.user1_id = $userID or b.user2_id = $userID
                      )
                )), true, false) as baddies,
                if((exists (
                    select * from user_has_shoogle as uhs
                    where uhs.solo = 1
                      and uhs.shoogle_id = shoogles.id
                      and uhs.user_id = $userID
                )), true, false) as solo
            "))
            ->whereIn('id', $shooglesIDs)
            ->get()
            ->map(function ($item) {
                $item['baddies'] = (bool)$item['baddies'];
                $item['solo'] = (bool)$item['solo'];
                $item['isPresent'] = HelperShoogle::isMember(Auth::id(), $item['id']);
                return $item;
            })
            ->toArray();

        $this->shoogles = $shoogles;
        $this->shooglesCount = count($shoogles);
    }
}
