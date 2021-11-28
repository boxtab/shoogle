<?php

namespace App\Helpers;

use App\Models\Shoogle;
use App\Models\ShoogleViews;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Class HelperShoogleViews
 * @package App\Helpers
 */
class HelperShoogleViews
{
    /**
     * Get shoogle by id.
     *
     * @param int|null $shoogleId
     * @return bool|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    private static function getShoogle(?int $shoogleId)
    {
        if ( is_null($shoogleId) ) {
            return false;
        }

        $shoogle = Shoogle::on()->where('id', '=', $shoogleId)->first();
        if ( is_null($shoogle) ) {
            return false;
        }

        return $shoogle;
    }

    /**
     * Get user by id.
     *
     * @param int|null $userId
     * @return bool|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    private  static function getUser(?int $userId)
    {
        if ( is_null($userId) ) {
            return false;
        }

        $user = User::on()->where('id', '=', $userId)->first();
        if ( is_null($user) ) {
            return false;
        }

        return $user;
    }

    /**
     * Delete user views.
     *
     * @param int|null $shoogleId
     * @param int|null $userId
     */
    public static function delete(?int $shoogleId, ?int $userId)
    {
        if ( self::getShoogle($shoogleId) === false ) {
            return;
        }

        if ( self::getUser($userId) ) {
            return;
        }

        ShoogleViews::on()
            ->where('shoogle_id', '=', $shoogleId)
            ->where('user_id', '=', $userId)
            ->delete();
    }

    /**
     * Delete shoogle by ID.
     *
     * @param int|null $shoogleId
     */
    public static function deleteById(?int $shoogleId)
    {
        if ( self::getShoogle($shoogleId) === false ) {
            return;
        }

        ShoogleViews::on()
            ->where('shoogle_id', '=', $shoogleId)
            ->delete();
    }

    /**
     * Increase views counter.
     *
     * @param int|null $shoogleId
     * @param int|null $userId
     */
    public static function increment(?int $shoogleId, ?int $userId)
    {
        $shoogle = self::getShoogle($shoogleId);
        if ( $shoogle === false ) {
            return;
        }

        if ( self::getUser($userId) === false ) {
            return;
        }

        $shoogleViews = ShoogleViews::on()
            ->where('shoogle_id', $shoogleId)
            ->where('user_id', $userId)
            ->first();

        if ( is_null($shoogleViews) ) {
            $userViews = 1;
        } else {
            $userViews = is_null( $shoogleViews->views ) ? 0 : $shoogleViews->views;
            $userViews++;
        }

        $generalViews = $shoogle->views++;;

        DB::transaction(function() use($shoogleId, $userId, $userViews, $generalViews) {

            Shoogle::on()->where('id', '=', $shoogleId)
                ->update([
                    'views' => $generalViews
                ]);

            ShoogleViews::on()->updateOrCreate(
                [
                    'shoogle_id' =>  $shoogleId,
                    'user_id' => $userId,
                ],
                [
                    'last_view' => Carbon::now(),
                    'views' => $userViews,
                ]
            );
        });
    }
}
