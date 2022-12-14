<?php

namespace App\Repositories;

use App\Constants\NotificationsTypeConstant;
use App\Constants\NotificationTextConstant;
use App\Enums\BuddyRequestTypeEnum;
use App\Helpers\Helper;
use App\Helpers\HelperAvatar;
use App\Helpers\HelperBuddies;
use App\Helpers\HelperBuddyRequest;
use App\Helpers\HelperCalendar;
use App\Helpers\HelperFriend;
use App\Helpers\HelperMember;
use App\Helpers\HelperNotifications;
use App\Helpers\HelperRequest;
use App\Helpers\HelperShoogle;
use App\Helpers\HelperShoogleViews;
use App\Http\Resources\ShoogleBuddyNameResource;
use App\Models\Buddie;
use App\Models\BuddyRequest;
use App\Models\Shoogle;
use App\Models\ShoogleViews;
use App\Models\UserHasShoogle;
use App\Scopes\ShoogleScope;
use App\Scopes\UserHasShoogleScope;
use App\Services\StreamService;
use App\Support\ApiResponse\ApiResponse;
use App\Traits\ShoogleCountTrait;
use App\Traits\ShoogleTrait;
use App\Traits\ShoogleValidationTrait;
use App\User;
use Carbon\Carbon;
use Exception;
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

            $memberId = UserHasShoogle::on()->create([
                'user_id'           => $shoogleField['owner_id'],
                'shoogle_id'        => $shoogleId,
                'joined_at'         => Carbon::now(),
                'solo'              => false,
                'reminder'          => $shoogleField['reminder'],
                'reminder_interval' => $shoogleField['reminder_interval'],
                'is_reminder'       => $shoogleField['is_reminder'],
            ])->id;

            HelperShoogleViews::increment($shoogleId, $shoogleField['owner_id']);

            $streamService = new StreamService($shoogleId);

            $channelShoogleId = $streamService->createChannelForShoogle($shoogleField['title'], $shoogleField['cover_image']);
            $this->model->on()
                ->where('id', '=', $shoogleId)
                ->update(['chat_id' => $channelShoogleId]);

            $channelMemberId = $streamService->createJournalChannel();
            UserHasShoogle::on()
                ->where('id', '=', $memberId)
                ->update(['chat_id' => $channelMemberId]);

            return $shoogleId;
        });

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
        return Shoogle::on()
            ->withoutGlobalScope(ShoogleScope::class)
            ->select(DB::raw(
                'shoogles.id as shoogle_id, ' .
                'shoogles.title as shoogle_title, ' .
                'shoogles.active as shoogle_active, ' .
                'shoogles.updated_at as shoogle_last_activity, ' .
                'users.first_name as users_first_name, ' .
                'users.last_name as users_last_name, ' .
                'users.profile_image as users_profile_image, ' .
                'null as shooglers, ' .
                'departments.name as departments_name '
            ))
            ->join('users', 'users.id', '=', 'shoogles.owner_id')
            ->leftJoin('departments', 'users.department_id', '=', 'departments.id')
            ->when( ! $this->noCompany(), function($query) {
                return $query->where('users.company_id', '=', $this->companyId);
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

        $shoogleViews = ShoogleViews::on()
            ->where('shoogle_id', $shoogleId)
            ->where('user_id', $userId)
            ->first();

        if ( is_null($shoogleViews) ) {
            $views = 1;
        } else {
            $views = is_null( $shoogleViews->views ) ? 0 : $shoogleViews->views;
            $views++;
        }

        $lastUpdate = ( ! is_null($shoogleViews) ) ? $shoogleViews->last_view : $timeAgo;


        if ( $lastUpdate->getTimestamp() <= $timeAgo->getTimestamp() ) {
            $shoogle = Shoogle::on()->where('id', $shoogleId)->first();
            $shoogle->views++;
            $shoogle->save();
        }

        ShoogleViews::on()->updateOrCreate(
            [
                'shoogle_id' =>  $shoogleId,
                'user_id' => Auth::id(),
            ],
            [
                'last_view' => Carbon::now(),
                'views' => $views,
            ]
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
     * @param int $userId
     * @param int $shoogleId
     * @param string|null $reminder
     * @param string|null $reminderInterval
     * @param bool|null $isReminder
     * @param bool|null $buddy
     * @param string|null $note
     * @throws \GetStream\StreamChat\StreamException
     * @throws \Exception
     */
    public function entry(
        int $userId,
        int $shoogleId,
        ?string $reminder,
        ?string $reminderInterval,
        ?bool $isReminder,
        ?bool $buddy,
        ?string $note): void
    {
        $member = UserHasShoogle::on()
            ->where('user_id', '=', $userId)
            ->where('shoogle_id', '=', $shoogleId)
            ->withoutGlobalScope(UserHasShoogleScope::class)
            ->withTrashed()
            ->first();

        DB::transaction(function() use($member, $userId, $shoogleId, $reminder, $reminderInterval, $isReminder, $buddy, $note) {

            if ( ! empty( $member ) ) {
                if ( ! is_null($member->left_at) || ! is_null($member->deleted_at) ) {
                    $affectedRows = $member->update([
                        'joined_at' => Carbon::now(),
                        'left_at' => null,

                        'solo' => (!$buddy),
                        'reminder' => $reminder,
                        'reminder_interval' => $reminderInterval,
                        'is_reminder' => $isReminder,

                        'deleted_at' => null,
                    ]);
                    $lastInsertId = $member->id;
                } else {
                    throw new \Exception("You are already a member of shoogle!",
                        Response::HTTP_UNPROCESSABLE_ENTITY);
                }
            } else {
                $userHasShoogle = UserHasShoogle::on()->create([
                    'user_id' => $userId,
                    'shoogle_id' => $shoogleId,
                    'joined_at' => Carbon::now(),

                    'solo' => ( ! $buddy ),
                    'reminder' => $reminder,
                    'reminder_interval' => $reminderInterval,
                    'is_reminder' => $isReminder,
                ]);
                $affectedRows = 1;
                $lastInsertId = $userHasShoogle->id;
            }

            if ( $affectedRows > 0 ) {

                HelperShoogleViews::increment($shoogleId, $userId);

                $streamService = new StreamService($shoogleId);
                $streamService->connectUserToChannel($this->model->chat_id, $note);
                $channelId = $streamService->createJournalChannel();
                UserHasShoogle::on()
                    ->where('id', '=', $lastInsertId)
                    ->update(['chat_id' => $channelId]);
            }

        });
    }

    /**
     * Shoogle exit method.
     *
     * @param int $shoogleId
     * @throws Exception
     */
    public function leave(int $shoogleId)
    {
        $shoogle = Shoogle::on()->where('id', '=', $shoogleId)->first();

        if ( is_null($shoogle) ) {
            throw new Exception("Shoogle doesn't exist.", Response::HTTP_NOT_FOUND);
        }

        if ( ! HelperShoogle::isMember( Auth::id(), $shoogleId ) ) {
            throw new Exception("You are not a member of the shoogle.", Response::HTTP_NOT_FOUND);
        }

        DB::transaction(function () use ($shoogle) {

            UserHasShoogle::on()
                ->where('user_id', Auth::id())
                ->where('shoogle_id', $shoogle->id)
                ->update(['left_at' => Carbon::now()]);

            HelperShoogleViews::delete($shoogle->id, Auth::id());

            $buddy = HelperBuddies::getBuddy($shoogle->id, Auth::id());
            if ( ! is_null($buddy) ) {
                Buddie::on()
                    ->where('id', '=', $buddy->id)
                    ->update([
                        'disconnected_at' => Carbon::now()
                    ]);
            }

            $buddyRequest = HelperBuddyRequest::getBuddyRequest($shoogle->id, Auth::id());
            if ( ! is_null($buddyRequest) ) {
                BuddyRequest::on()
                    ->where('id', '=', $buddyRequest->id)
                    ->update([
                        'type' => BuddyRequestTypeEnum::DISCONNECT,
                    ]);
            }

            $buddyUserId = HelperBuddies::getBuddyId($shoogle->id, Auth::id());
            if ( ! is_null( $buddyUserId ) ) {

                $helperNotification = new HelperNotifications();

                $helperNotification->sendNotificationToUser(
                    $buddyUserId,
                    NotificationsTypeConstant::BUDDY_DISCONNECT_ID,
                    Auth::user()->first_name . ' ' . Auth::user()->first_name . ' left ' . $shoogle->title . '.  You are no longer buddied.'
                );
                $helperNotification->recordNotificationDetail($shoogle->id, Auth::id() );
            }
        });
    }

    /**
     * List of shoogles for the currently authenticated user.
     *
     * @param int $page
     * @param int $pageSize
     * @return array
     */
    public function listShoogleOfAuthUser(int $page, int $pageSize)
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
                null as owner,
                sh.chat_id as chatNameCommon,
                null as chatNameWithBuddy
            '))
            ->whereIn('sh.id', $shoogleIDs)
            ->where('sh.active', '=', 1)
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
        $shoogles = $this->setChatNameWithBuddy($shoogles);

        return $shoogles;
    }

    /**
     * Search by shoogles.
     *
     * @param int $companyId
     * @param string|null $search
     * @param string|null $filterIncome
     * @param int|null $page
     * @param int|null $pageSize
     * @return array|null
     */
    public function search(int $companyId, string $search = null, string $filterIncome = null, int $page = null, int $pageSize = null)
    {
        switch ($filterIncome) {
            case 'oldest':
                $filter = 'asc';
                break;
            case 'newest':
                $filter = 'desc';
                break;
            default:
                $filter = 'popular';
        }

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
                null as joined,
                sh.chat_id as chatNameCommon,
                null as chatNameWithBuddy,
                (select count(*) from shoogles_views where shoogles_views.shoogle_id = sh.id ) as countView
            '))
            ->Join('users as u', 'sh.owner_id', '=', 'u.id')
            ->leftJoin('wellbeing_categories as wc', 'sh.wellbeing_category_id', '=', 'wc.id')
            ->whereNull('sh.deleted_at')
            ->where('sh.active', '=', 1)
            ->where('u.company_id', '=', $companyId)
            ->when( ! is_null($search), function($query) use ($search) {
                return $query->where(function ($query) use ($search) {
                    return $query->where('sh.title', 'LIKE', '%' . $search . '%')
                        ->orWhere('sh.description', 'LIKE', '%' . $search . '%')
                        ->orWhere('wc.name', 'LIKE', '%' . $search . '%');
                });
            })
            ->when( $filter !== 'popular', function($query) use ($filter) {
                return $query->orderBy('sh.created_at', $filter);
            })
            ->when( $filter === 'popular', function($query) use ($filter) {
                return $query->orderBy('countView', 'desc');
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

        $shoogles = $this->setChatNameWithBuddy($shoogles);

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
     * Unique number of communities.
     *
     * @return int
     */
    public function getCommunityCount(): int
    {
        if ( is_null( $this->shooglesAll ) ) {
            return 0;
        }

        $shoogleIDs = [];
        foreach ( $this->shooglesAll as $shoogle ) {
            $shoogleIDs[] = $shoogle->id;
        }

        $membersIDs = UserHasShoogle::on()
            ->whereIn('shoogle_id', $shoogleIDs)
            ->get('user_id')
            ->map(function ($item) {
                return $item->user_id;
            })
            ->toArray();
        $membersIDs = array_unique($membersIDs);

        return count($membersIDs);
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
     * @param UserHasShoogle|null $member
     * @return array
     */
    public function getCalendar(Shoogle $shoogle, ?UserHasShoogle $member): array
    {
        $friend = HelperFriend::getFriend($shoogle->id, Auth::id());

        return [
            'shoogleId'         => $shoogle->id,
            'title'             => $shoogle->title,
            'coverImage'        => $shoogle->cover_image,
            'reminder'          => ( ! is_null($member) ) ? $member->reminder_formatted : null,
            'reminderInterval'  => ( ! is_null($member) ) ? $member->reminder_interval : null,
            'buddyName'         => (new ShoogleBuddyNameResource($friend)),
            'buddy'             => HelperCalendar::getBuddy( $shoogle->id, Auth::id() ),
            'isOwner'           => HelperShoogle::isOwner( Auth::id(), $shoogle->id ),
            'isMember'          => HelperMember::isMember( $shoogle->id, Auth::id() ),
            'isReminder'        => ( ! is_null($member) ) ? $member->is_reminder : null,
            'shooglersCount'    => HelperShoogle::getShooglersCount($shoogle->id),
        ];
    }

    /**
     * Setting preferences.
     *
     * @param UserHasShoogle $member
     * @param array $setting
     */
    public function setSetting(UserHasShoogle $member, array $setting)
    {
        $toSave = false;
        if ( array_key_exists('reminder', $setting) ) {
            $member->reminder = $setting['reminder'];
            $toSave = true;
        }

        if ( array_key_exists('reminderInterval', $setting) ) {
            $member->reminder_interval = $setting['reminderInterval'];
            $toSave = true;
        }

        if ( array_key_exists('buddy', $setting) ) {
            $member->solo = ! (bool)$setting['buddy'];
            $toSave = true;
        }

        if ( array_key_exists('isReminder', $setting) ) {
            $member->is_reminder = (bool)$setting['isReminder'];
            $toSave = true;
        }

        if ( $toSave ) {
            $member->save();
        }
    }

    /**
     * Destroy shoogle.
     *
     * @param Shoogle $shoogle
     * @param int $id
     */
    public function destroy(Shoogle $shoogle, int $id)
    {
        DB::transaction(function () use ($shoogle, $id) {
            HelperShoogleViews::deleteById($id);
            $shoogle->destroy($id);
        });
    }
}
