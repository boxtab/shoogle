<?php

namespace App\Repositories;

use App\Helpers\Helper;
use App\Models\Shoogle;
use App\Models\UserHasShoogle;
use App\Traits\ShooglerTrait;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class ShooglerRepository
 * @package App\Repositories
 */
class ShooglerRepository extends Repositories
{
    use ShooglerTrait;

    /**
     * @var Shoogle
     */
    protected $model;

    /**
     * @var int
     */
    private $shoogleID;

    /**
     * ShooglerRepository constructor.
     * @param UserHasShoogle $model
     */
    public function __construct(UserHasShoogle $model)
    {
        parent::__construct($model);
    }

    /**
     * List of shooglers.
     *
     * @param int $shoogleId
     * @param string|null $search
     * @param string|null $filter
     * @param int|null $page
     * @param int|null $pageSize
     * @return array
     */
    public function getShooglerList(int $shoogleId, string $search = null, string $filter = null, int $page = null, int $pageSize = null)
    {
        $this->shoogleID = $shoogleId;
        $shooglersIDs = $this->getShooglersIDsByShoogleID($shoogleId);

        $shooglers = DB::table('users as u')
            ->select(DB::raw('
                u.id as id,
                u.profile_image as profile_image,
                u.first_name as firstName,
                u.last_name as lastName,
                u.about as about,
                null as baddies,
                null as solo,
                null as joinedAt
            '))
            ->whereIn('u.id', $shooglersIDs)
            ->when( ! is_null($search), function($query) use ($search) {
                return $query->where(function ($query) use ($search) {
                    return $query->where('u.first_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('u.email', 'LIKE', '%' . $search . '%');
                });
            })
            ->offset($page * $pageSize - $pageSize)
            ->limit($pageSize)
            ->get()
            ->toArray();

        $shooglers = $this->setBaddies($shooglers);
        $shooglers = $this->setSolo($shooglers);
        $shooglers = $this->setJoinedAt($shooglers);

        return $shooglers;

        // ['recentlyJoined', 'available', 'solo', 'buddied'])
//        return DB::table('user_has_shoogle as uhs')
//            ->select(DB::raw('
//                u.id as id,
//                u.avatar as photo,
//                u.first_name as firstName,
//                u.last_name as lastName,
//                u.about as about,
//                exists(
//                    select b.*
//                    from buddies as b
//                    where b.shoogle_id = ' . $shoogleId . '
//                      and (b.user1_id = u.id or b.user2_id = u.id)
//                ) as buddied,
//                false as solo,
//                uhs.joined_at as joinedAt
//            '))
//            ->leftJoin('users as u', 'uhs.user_id', '=', 'u.id')
//            ->where('uhs.shoogle_id', '=', $shoogleId)
//            ->when( ! is_null($search), function($query) use ($search) {
//                return $query->where(function ($query) use ($search) {
//                    return $query->where('u.first_name', 'LIKE', '%' . $search . '%')
//                    ->orWhere('u.email', 'LIKE', '%' . $search . '%');
//                });
//            })
//            ->when($filter === 'recentlyJoined', function ($query) {
//                return $query->where( 'joined_at', '<=', Carbon::now()->subDays(1)->toDateTimeString() );
//            })
//            ->when($filter === 'available', function ($query) use ($shoogleId) {
//                return $query->whereNotExists(function ($query) use ($shoogleId) {
//                    $query->select('buddies.id')
//                        ->from('buddies')
//                        ->whereRaw('buddies.user1_id = u.id or buddies.user2_id = u.id and buddies.shoogle_id = ' . $shoogleId);
//                });
//            })
//            ->when($filter === 'buddied', function ($query) use ($shoogleId) {
//                return $query->whereExists(function ($query) use ($shoogleId) {
//                    $query->select('buddies.id')
//                        ->from('buddies')
//                        ->whereRaw('buddies.user1_id = u.id or buddies.user2_id = u.id and buddies.shoogle_id = ' . $shoogleId);
//                });
//            })
//            ->offset($page * $pageSize - $pageSize)
//            ->limit($pageSize)
//            ->get()
//            ->toArray();
    }
}

