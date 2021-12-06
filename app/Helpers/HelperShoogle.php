<?php

namespace App\Helpers;

use App\Models\Shoogle;
use App\Models\UserHasShoogle;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use NunoMaduro\Collision\Exceptions\ShouldNotHappen;

/**
 * Class HelperShoogle
 * @package App\Helpers
 */
class HelperShoogle
{
    /**
     * Is a user a member of shoogle.
     *
     * @param int|null $userID
     * @param int|null $shoogleID
     * @return bool
     */
    public static function isMember(?int $userID, ?int $shoogleID): bool
    {
        if ( is_null($userID) || is_null($shoogleID) ) {
            return false;
        }

        $userHasShoogleCount = UserHasShoogle::on()
            ->where('shoogle_id', '=', $shoogleID)
            ->where('user_id', '=', $userID)
            ->count();

        return ($userHasShoogleCount > 0) ? true : false;
    }

    /**
     * Is the user the owner of a shoogle.
     *
     * @param int|null $userID
     * @param int|null $shoogleID
     * @return bool
     */
    public static function isOwner(?int $userID, ?int $shoogleID): bool
    {
        if ( is_null($userID) || is_null($shoogleID) ) {
            return false;
        }

        return Shoogle::on()
            ->where('id' , '=', $shoogleID)
            ->where('owner_id', '=', $userID)
            ->exists();
    }

    /**
     * Return shoogle by identifier or generate an error.
     *
     * @param int|null $shoogleId
     * @return Shoogle|\Illuminate\Database\Eloquent\Builder|Model|object
     * @throws \Exception
     */
    public static function getShoogle(?int $shoogleId): Shoogle
    {
        $shoogle = Shoogle::on()->where('id', '=', $shoogleId)->first();

        if ( is_null( $shoogle ) ) {
            throw new \Exception("Shoogle not found!", Response::HTTP_NOT_FOUND);
        }

        return $shoogle;
    }

    /**
     * Returns the number of members for a shoogle.
     *
     * @param int|null $shoogleId
     * @return int
     */
    public static function getShooglersCount(?int $shoogleId): int
    {
        if ( is_null($shoogleId) ) {
            return 0;
        }

        return UserHasShoogle::on()
            ->leftJoin('shoogles', 'user_has_shoogle.shoogle_id', '=', 'shoogles.id')
            ->where('user_has_shoogle.shoogle_id', '=', $shoogleId)
            ->whereNull('shoogles.deleted_at')
            ->where('shoogles.active', '=', 1)
            ->count('user_has_shoogle.user_id');
    }

    /**
     * The count of shoogles per user.
     *
     * @param int|null $userId
     * @param bool $blocked
     * @return int
     */
    public static function getShoogleCount(?int $userId, bool $blocked = false): int
    {
        if ( is_null($userId) ) {
            return 0;
        }

        return UserHasShoogle::on()
            ->leftJoin('shoogles', 'user_has_shoogle.shoogle_id', '=', 'shoogles.id')
            ->where('user_has_shoogle.user_id', '=', $userId)
            ->whereNull('shoogles.deleted_at')
            ->when( $blocked === false, function ($query) {
                $query->where('shoogles.active', '=', 1);
            })
            ->count('user_has_shoogle.shoogle_id');
    }

    /**
     * Get shoogle title by id.
     *
     * @param int|null $shoogleId
     * @return string
     */
    public static function getTitle(?int $shoogleId): string
    {
        if ( is_null( $shoogleId ) ) {
            return 'No shoogle ID passed.';
        }

        $shoogle = Shoogle::on()
            ->where('id', '=', $shoogleId)
            ->first();

        if ( is_null( $shoogle ) ) {
            return 'No shoogle found with this ID.';
        }

        return $shoogle->title;
    }

    /**
     * Returns true if shoogle is active.
     *
     * @param int|null $shoogleId
     * @return bool
     */
    public static function isActive(?int $shoogleId): bool
    {
        if ( is_null( $shoogleId ) ) {
            return false;
        }

        $shoogle = Shoogle::on()
            ->where('id', '=', $shoogleId)
            ->where('active', '=', 1)
            ->first();

        return ! is_null( $shoogle ) ? true : false;
    }

    /**
     * Check if shoogle is active.
     *
     * @param int|null $shoogleId
     * @throws Exception
     */
    public static function checkActive(?int $shoogleId)
    {
        if ( ! self::isActive($shoogleId) ) {
            throw new Exception('Shoogle is not active!', Response::HTTP_NOT_FOUND);
        }
    }
}
