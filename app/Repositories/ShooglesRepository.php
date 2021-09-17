<?php

namespace App\Repositories;

use App\Helpers\Helper;
use App\Helpers\HelperRequest;
use App\Models\Shoogle;
use App\Models\ShoogleViews;
use App\Models\UserHasShoogle;
use App\Support\ApiResponse\ApiResponse;
use App\Traits\ShoogleTrait;
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
    use ShoogleTrait;

    /**
     * @var Shoogle
     */
    protected $model;

    /**
     * @var array
     */
    private $shooglesAll;

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
        $shoogleIDs = $this->getShoogleIDsByUserId( Auth::id() );

        $shoogles = DB::table('shoogles as sh')
            ->select(DB::raw('
                sh.id as id,
                sh.title as title,
                sh.cover_image as coverImage,
                null as shooglersCount,
                null as buddiesCount,
                null as solosCount,
                null as buddyName,
                null as solo
            '))
            ->whereIn('sh.id', $shoogleIDs)
            ->offset($page * $pageSize - $pageSize)
            ->limit($pageSize)
            ->get()
            ->toArray();

        $shoogles = $this->setShooglersCount($shoogles);
        $shoogles = $this->setBuddiesCount($shoogles);
        $shoogles = $this->setSolosCount($shoogles);
        $shoogles = $this->setBuddy($shoogles);
        $shoogles = $this->setSoloMode($shoogles);

        return $shoogles;
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
        $shooglesQuery = DB::table('shoogles as sh')
            ->select(DB::raw('
                sh.id as id,
                sh.title as title,
                sh.cover_image as coverImage,
                null as shooglersCount,
                null as buddiesCount,
                null as solosCount,
                null as buddyName,
                null as solo,
                null as joined
            '))
            ->leftJoin('wellbeing_categories as wc', 'sh.wellbeing_category_id', '=', 'wc.id')
            ->when( ! is_null($search), function($query) use ($search) {
                return $query->where(function ($query) use ($search) {
                    return $query->where('sh.title', 'LIKE', '%' . $search . '%')
                        ->orWhere('sh.description', 'LIKE', '%' . $search . '%')
                        ->orWhere('wc.name', 'LIKE', '%' . $search . '%');
                });
            })
            ->when( ! is_null($order), function($query) use ($order) {
                return $query->orderBy('sh.created_at', $order);
            });

        $this->shooglesAll = $shooglesQuery
            ->get()
            ->toArray();

        $this->shooglesAll = $this->setShooglersCount($this->shooglesAll);
        $this->shooglesAll = $this->setBuddiesCount($this->shooglesAll);
        $this->shooglesAll = $this->setSolosCount($this->shooglesAll);

        $shoogles = $shooglesQuery
            ->offset($page * $pageSize - $pageSize)
            ->limit($pageSize)
            ->get()
            ->toArray();

        $shoogles = $this->setShooglersCount($shoogles);
        $shoogles = $this->setBuddiesCount($shoogles);
        $shoogles = $this->setSolosCount($shoogles);
        $shoogles = $this->setBuddy($shoogles);
        $shoogles = $this->setSoloMode($shoogles);
        $shoogles = $this->setJoined($shoogles);

        return $shoogles;
    }

    /**
     * Number of results found.
     *
     * @return int
     */
    public function getFindCount(): int
    {
        if ( is_null( $this->shooglesAll ) ) {
            return 0;
        }

        return count($this->shooglesAll);
    }

    /**
     * Community count.
     *
     * @return int
     */
    public function getCommunityCount(): int
    {
        if ( is_null( $this->shooglesAll ) ) {
            return 0;
        }

        $communityCount = 0;
        foreach ( $this->shooglesAll as $shoogle ) {
            $communityCount += $shoogle->shooglersCount;
        }
        return $communityCount;
    }

    /**
     * Buddies count.
     *
     * @return int
     */
    public function getBuddiesCount(): int
    {
        if ( is_null( $this->shooglesAll ) ) {
            return 0;
        }

        $buddiesCount = 0;
        foreach ( $this->shooglesAll as $shoogle ) {
            $buddiesCount += $shoogle->buddiesCount;
        }
        return $buddiesCount;
    }

    /**
     * Solos count.
     *
     * @return int
     */
    public function getSolosCount(): int
    {
        if ( is_null( $this->shooglesAll ) ) {
            return 0;
        }

        $solosCount = 0;
        foreach ( $this->shooglesAll as $shoogle ) {
            $solosCount += $shoogle->solosCount;
        }
        return $solosCount;
    }
}

