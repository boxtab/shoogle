<?php

namespace App\Repositories;

use App\Helpers\Helper;
use App\Helpers\HelperAvatar;
use App\Helpers\HelperBuddies;
use App\Helpers\HelperFriend;
use App\Helpers\HelperMember;
use App\Helpers\HelperRequest;
use App\Helpers\HelperShoogle;
use App\Http\Resources\ShoogleBuddyNameResource;
use App\Models\Shoogle;
use App\Models\ShoogleViews;
use App\Models\UserHasShoogle;
use App\Services\StreamService;
use App\Support\ApiResponse\ApiResponse;
use App\Traits\ShoogleCountTrait;
use App\Traits\ShoogleTrait;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use GetStream\StreamChat\Client as StreamClient;

/**
 * Class ShooglesRepository
 * @package App\Repositories
 */
class ShooglesRepository extends Repositories
{
    use ShoogleTrait, ShoogleCountTrait;

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
     * Create a shoogle.
     *
     * @param array $shoogleField
     * @return int
     * @throws \GetStream\StreamChat\StreamException
     */
    public function createShoogle(array $shoogleField): int
    {
        $shoogleId = DB::transaction(function() use ($shoogleField) {

            $shoogleId = $this->model->on()->create([
                'owner_id'              => $shoogleField['owner_id'],
                'wellbeing_category_id' => $shoogleField['wellbeing_category_id'],
                'active'                => $shoogleField['active'],
                'title'                 => $shoogleField['title'],
                'cover_image'           => $shoogleField['cover_image'],
            ])->id;

            UserHasShoogle::on()->create([
                'user_id'           => $shoogleField['owner_id'],
                'shoogle_id'        => $shoogleId,
                'joined_at'         => Carbon::now(),
                'solo'              => false,
                'reminder'          => $shoogleField['reminder'],
                'reminder_interval' => $shoogleField['reminder_interval'],
                'is_reminder'       => $shoogleField['is_reminder'],
            ]);

            return $shoogleId;
        });


        $streamService = new StreamService($shoogleId);
        $this->model->on()
            ->where('id', '=', $shoogleId)
            ->update(['chat_id' => $streamService->getChatId()]);

        return $shoogleId;
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
     * Shoogle entry method.
     *
     * @param int $userID
     * @param int $shoogleId
     * @param string|null $reminder
     * @param string|null $reminderInterval
     * @param bool|null $isReminder
     * @param bool|null $buddy
     * @param string|null $note
     * @throws \GetStream\StreamChat\StreamException
     */
    public function entry(
        int $userID,
        int $shoogleId,
        ?string $reminder,
        ?string $reminderInterval,
        ?bool $isReminder,
        ?bool $buddy,
        ?string $note): void
    {
        if ( ! Shoogle::on()->where('id', '=', $shoogleId)->exists() ) {
            throw new \Exception("Shoogle ID $shoogleId does not exist or has been deleted", Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $member = UserHasShoogle::on()
            ->where('user_id', '=', $userID)
            ->where('shoogle_id', '=', $shoogleId)
            ->where(function ($query) {
                $query->whereNotNull('left_at')
                    ->orWhereNotNull('deleted_at');
            })
            ->withTrashed()
            ->first();

        if ( ! empty($member) ) {
            $affectedRows = $member->update([
                'joined_at' => Carbon::now(),
                'left_at' => null,

                'solo' => ( ! $buddy ),
                'reminder' => $reminder,
                'reminder_interval' => $reminderInterval,
                'is_reminder' => $isReminder,

                'deleted_at' => null,
            ]);
        } else {
            $affectedRows = UserHasShoogle::on()->create([
                'user_id' => $userID,
                'shoogle_id' => $shoogleId,
                'joined_at' => Carbon::now(),

                'solo' => ( ! $buddy ),
                'reminder' => $reminder,
                'reminder_interval' => $reminderInterval,
                'is_reminder' => $isReminder,
            ])->exists();
        }

        if ( $affectedRows > 0 ) {
            $streamService = new StreamService($shoogleId);
            $streamService->addMembers();
        }
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
                null as solo,
                null as owner
            '))
            ->whereIn('sh.id', $shoogleIDs)
            ->offset($page * $pageSize - $pageSize)
            ->limit($pageSize)
            ->get()
            ->toArray();

        $shoogles = $this->setShooglersCount($shoogles);
        $shoogles = $this->setGeneralBuddiesCount($shoogles);
        $shoogles = $this->setSolosCount($shoogles);

        $shoogles = $this->setBuddy($shoogles);
        $shoogles = $this->setSoloMode($shoogles);
        $shoogles = $this->setOwner($shoogles);

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

        $this->shooglesAll = $this->setGeneralShooglersCount($this->shooglesAll);
        $this->shooglesAll = $this->setGeneralBuddiesCount($this->shooglesAll);
        $this->shooglesAll = $this->setGeneralSolosCount($this->shooglesAll);

        $shoogles = $shooglesQuery
            ->offset($page * $pageSize - $pageSize)
            ->limit($pageSize)
            ->get()
            ->toArray();

        $shoogles = $this->setGeneralShooglersCount($shoogles);
        $shoogles = $this->setGeneralBuddiesCount($shoogles);
        $shoogles = $this->setGeneralSolosCount($shoogles);

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

    /**
     * User calendar settings for shoogle.
     *
     * @param Shoogle $shoogle
     * @param UserHasShoogle $member
     * @return array
     */
    public function getCalendar(Shoogle $shoogle, UserHasShoogle $member): array
    {
        $friend = HelperFriend::getFriend($shoogle->id, Auth::id());

        return [
            'shoogleId'         => $shoogle->id,
            'title'             => $shoogle->title,
            'coverImage'        => $shoogle->cover_image,
            'reminder'          => $member->reminder_formatted,
            'reminderInterval'  => $member->reminder_interval,
            'buddyName'         => (new ShoogleBuddyNameResource($friend)),
            'buddy'             => HelperFriend::haveFriend( $shoogle->id, Auth::id() ),
            'isOwner'           => HelperShoogle::isOwner(Auth::id(), $shoogle->id),
            'isMember'          => HelperMember::isMember($shoogle->id, Auth::id()),
        ];
    }
}

