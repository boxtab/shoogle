<?php

namespace App\Repositories;

use App\Helpers\Helper;
use App\Models\Shoogle;
use App\Models\ShoogleViews;
use App\Models\UserHasShoogle;
use App\Support\ApiResponse\ApiResponse;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class ShooglesRepository
 * @package App\Repositories
 */
class ShooglesRepository extends Repositories
{
    /**
     * @var Shoogle
     */
    protected $model;

    /**
     * ShoogleRepository constructor.
     *
     * @param Shoogle $model
     */
    public function __construct(Shoogle $model)
    {
        parent::__construct($model);
    }

    /**
     * List of shoogles.
     *
     * @param string $search
     * @return mixed
     */
    public function getList(string $search = null)
    {
        return Shoogle::on()->select(DB::raw(
                'shoogles.id as shoogle_id, ' .
                'shoogles.title as shoogle_title, ' .
                'shoogles.active as shoogle_active, ' .
                'shoogles.updated_at as shoogle_last_activity, ' .
                'users.first_name as users_first_name, ' .
                'users.last_name as users_last_name, ' .
                'users.profile_image as users_profile_image, ' .
                '(select count(uhs.user_id) from user_has_shoogle as uhs where uhs.shoogle_id = shoogles.id) + 1 as shooglers, ' .
                'departments.name as departments_name '
            ))
            ->leftJoin('users', 'users.id', '=', 'shoogles.owner_id')
            ->leftJoin('departments', 'users.department_id', '=', 'departments.id')
            ->when( ! $this->noCompany(), function($query) {
                return $query->where('users.company_id', $this->companyId);
            })
            ->when( ! is_null( $search ) , function ($query) use ($search) {
                return $query->where('title', 'like', '%' . $search .'%');
            })
            ->get();
    }

    /**
     * Increase views counter.
     *
     * @param int $shoogleId
     */
    public function incrementViews(int $shoogleId): void
    {
        $userId = Auth::id();
//        $hourAgo = Carbon::now()->subHours(1);
        $minuteAgo = Carbon::now()->subMinute();
        $timeAgo = $minuteAgo;

        $shoogleViews = ShoogleViews::where('shoogle_id', $shoogleId)->where('user_id', $userId)->first();

        $lastUpdate = ( ! is_null($shoogleViews) ) ? $shoogleViews->last_view : $timeAgo;


        if ( $lastUpdate->getTimestamp() <= $timeAgo->getTimestamp() ) {
            $shoogle = Shoogle::where('id', $shoogleId)->first();
            $shoogle->views++;
            $shoogle->save();
        }

        ShoogleViews::updateOrCreate(
            [
                'shoogle_id' =>  $shoogleId,
                'user_id' => Auth::id(),
            ],
            ['last_view' => Carbon::now()]
        );
    }

    /**
     * Change solo mode.
     *
     * @param int $shoogleId
     * @param bool $flag
     */
    public function soloChange(int $shoogleId, bool $flag): void
    {
        UserHasShoogle::on()
            ->where('user_id', Auth::id())
            ->where('shoogle_id', $shoogleId)
            ->update(['solo' => ((int) $flag)]);
    }

    /**
     * Shoogle exit method.
     *
     * @param int $shoogleId
     */
    public function leave(int $shoogleId)
    {
        UserHasShoogle::on()
            ->where('user_id', Auth::id())
            ->where('shoogle_id', $shoogleId)
            ->delete();
    }

    /**
     * List of user shoogles.
     *
     * @param int $page
     * @param int $pageSize
     * @return array
     */
    public function userList(int $page, int $pageSize)
    {
        $query = DB::table('shoogles as sh')
            ->select(DB::raw('
                sh.id as id,
                sh.title as title,
                sh.cover_image as coverImage,
                ifnull(shooglers_count.count_user, 0) as shooglersCount,
                ifnull(shooglers_buddies.count_user, 0) as buddiesCount,
                ifnull(shooglers_solo.count_user, 0) as solosCount,
                null as buddyName,
                null as solo
            '))
            ->join(DB::raw('
                (select
                    shoogle_id
                    from user_has_shoogle
                    where user_id = ' . Auth::id() .' ) as uhs'),
                'uhs.shoogle_id', '=', 'sh.id'
            )
            ->leftJoin(DB::raw('
                    (select
                        user_has_shoogle.shoogle_id as unique_shoogle_id,
                        count(user_has_shoogle.user_id) + 1 as count_user
                    from user_has_shoogle
                    group by user_has_shoogle.shoogle_id)
                    as shooglers_count
             '), 'shooglers_count.unique_shoogle_id', '=', 'sh.id')
            ->leftJoin(DB::raw('
                    (select
                        user_has_shoogle.shoogle_id as unique_shoogle_id,
                        count(user_has_shoogle.user_id) as count_user
                    from user_has_shoogle
                         where exists(
                                        select 1 from buddies
                                        where user_has_shoogle.shoogle_id = buddies.shoogle_id
                                        and (user_has_shoogle.user_id = buddies.user1_id or user_has_shoogle.user_id = buddies.user2_id)
                                   )
                    group by user_has_shoogle.shoogle_id)
                    as shooglers_buddies
             '), 'shooglers_buddies.unique_shoogle_id', '=', 'sh.id')
            ->leftJoin(DB::raw('
                    (select
                        user_has_shoogle.shoogle_id as unique_shoogle_id,
                        count(user_has_shoogle.user_id) as count_user
                    from user_has_shoogle
                    where user_has_shoogle.solo = 1
                    group by user_has_shoogle.shoogle_id)
                    as shooglers_solo
             '), 'shooglers_solo.unique_shoogle_id', '=', 'sh.id')
            ->where('sh.owner_id', '=', Auth::id())
            ->offset($page * $pageSize - $pageSize)
            ->limit($pageSize);

        return $query->get()->toArray();

        /*
        $query = DB::table('user_has_shoogle as uhs')
            ->select(DB::raw('
                uhs.shoogle_id as id,
                sh.title as title,
                sh.cover_image as coverImage,
                ifnull(shooglers_count.count_user, 0) as shooglersCount,
                ifnull(shooglers_buddies.count_user, 0) as buddiesCount,
                ifnull(shooglers_solo.count_user, 0) as solosCount,
                null as buddyName,
                uhs.solo as solo
            '))
            ->leftJoin('shoogles as sh', 'uhs.shoogle_id', '=', 'sh.id')
            ->leftJoin(DB::raw('
                    (select
                        user_has_shoogle.shoogle_id as unique_shoogle_id,
                        count(user_has_shoogle.user_id) as count_user
                    from user_has_shoogle
                    group by user_has_shoogle.shoogle_id)
                    as shooglers_count
             '), 'shooglers_count.unique_shoogle_id', '=', 'uhs.shoogle_id')
            ->leftJoin(DB::raw('
                    (select
                        user_has_shoogle.shoogle_id as unique_shoogle_id,
                        count(user_has_shoogle.user_id) as count_user
                    from user_has_shoogle
                         where exists(
                                        select 1 from buddies
                                        where user_has_shoogle.shoogle_id = buddies.shoogle_id
                                        and (user_has_shoogle.user_id = buddies.user1_id or user_has_shoogle.user_id = buddies.user2_id)
                                   )
                    group by user_has_shoogle.shoogle_id)
                    as shooglers_buddies
             '), 'shooglers_buddies.unique_shoogle_id', '=', 'uhs.shoogle_id')
            ->leftJoin(DB::raw('
                    (select
                        user_has_shoogle.shoogle_id as unique_shoogle_id,
                        count(user_has_shoogle.user_id) as count_user
                    from user_has_shoogle
                    where user_has_shoogle.solo = 1
                    group by user_has_shoogle.shoogle_id)
                    as shooglers_solo
             '), 'shooglers_solo.unique_shoogle_id', '=', 'uhs.shoogle_id')
            ->where('uhs.user_id', Auth::id())
            ->offset($page * $pageSize - $pageSize)
            ->limit($pageSize);

        return $query->get()->toArray();
        */
    }

    /**
     * Search by shoogles.
     *
     * @param string|null $search
     * @param string|null $order
     * @param int|null $page
     * @param int|null $pageSize
     * @return array
     */
    public function search(string $search = null, string $order = null, int $page = null, int $pageSize = null)
    {
        $innerOrder = ($order === 'asc' || $order === 'desc') ? $order : 'desc';

        $query = DB::table('shoogles as sh')
            ->select(DB::raw('
                sh.id as id,
                sh.title as title,
                sh.cover_image as coverImage,
                ifnull(shooglers_count.count_user, 0) as shooglersCount,
                ifnull(shooglers_buddies.count_user, 0) as buddiesCount,
                ifnull(shooglers_solo.count_user, 0) as solosCount,
                null as buddyName,
                if(
                    (
                        select count(*)
                        from user_has_shoogle as uhs
                        where uhs.shoogle_id = sh.id
                          and uhs.solo=1
                          and uhs.user_id = '. Auth::id() .'
                    ) = 0,
                0, 1) as solo,
                if(
                    (
                        select count(*)
                        from user_has_shoogle as uhs
                        where uhs.shoogle_id = sh.id
                          and uhs.user_id = '. Auth::id() .'
                    ) = 0,
                0, 1) as joined
            '))
            ->leftJoin(DB::raw('
                    (select
                        user_has_shoogle.shoogle_id as unique_shoogle_id,
                        count(user_has_shoogle.user_id) as count_user
                    from user_has_shoogle
                    group by user_has_shoogle.shoogle_id)
                    as shooglers_count
            '), 'shooglers_count.unique_shoogle_id', '=', 'sh.id')
            ->leftJoin(DB::raw('
                    (select
                        user_has_shoogle.shoogle_id as unique_shoogle_id,
                        count(user_has_shoogle.user_id) as count_user
                    from user_has_shoogle
                         where exists(
                                        select 1 from buddies
                                        where user_has_shoogle.shoogle_id = buddies.shoogle_id
                                        and (user_has_shoogle.user_id = buddies.user1_id or user_has_shoogle.user_id = buddies.user2_id)
                                   )
                    group by user_has_shoogle.shoogle_id)
                    as shooglers_buddies
            '), 'shooglers_buddies.unique_shoogle_id', '=', 'sh.id')
            ->leftJoin(DB::raw('
                    (select
                        user_has_shoogle.shoogle_id as unique_shoogle_id,
                        count(user_has_shoogle.user_id) as count_user
                    from user_has_shoogle
                    where user_has_shoogle.solo = 1
                    group by user_has_shoogle.shoogle_id)
                    as shooglers_solo
            '), 'shooglers_solo.unique_shoogle_id', '=', 'sh.id')
            ->when( ! is_null($search), function ($query) use ($search) {
                return $query
                    ->where('sh.title', 'like', '%' . $search .'%')
                    ->orWhere('sh.description', 'like', '%' . $search .'%');
            })
            ->orderBy('sh.created_at', $innerOrder)
            ->offset($page * $pageSize - $pageSize)
            ->limit($pageSize);


//        $query->toSql();
        $searchResult = $query->get()->toArray();
        return $searchResult;
    }

}

