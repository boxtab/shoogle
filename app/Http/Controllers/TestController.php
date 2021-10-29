<?php

namespace App\Http\Controllers;

use App\Constants\RewardConstant;
use App\Helpers\Helper;
use App\Helpers\HelperBuddies;
use App\Helpers\HelperChat;
use App\Helpers\HelperDateTime;
use App\Helpers\HelperFriend;
use App\Helpers\HelperMember;
use App\Helpers\HelperNotific;
use App\Helpers\HelperNow;
use App\Helpers\HelperReward;
use App\Helpers\HelperShoogle;
use App\Helpers\HelperShoogleList;
use App\Helpers\HelperShoogleProfile;
use App\Helpers\HelperShoogleStatistic;
use App\Helpers\HelperShooglesViews;
use App\Helpers\HelperStream;
use App\Models\Company;
use App\Models\Invite;
use App\Models\ModelHasRole;
use App\Models\Shoogle;
use App\Models\UserHasShoogle;
use App\Models\WellbeingScores;
use App\Repositories\TestRepository;
use App\Services\NotificClientService;
use App\Services\RruleService;
use Carbon\Carbon;
use Database\Seeders\IconRewardsSeeder;
use DateInterval;
use DateTime;
use Illuminate\Http\Request;
use App\User;
use App\Constants\RoleConstant;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Recurr\Exception\InvalidRRule;
use Recurr\Exception\InvalidWeekday;
use Recurr\Rule;
use Recurr\Transformer\ArrayTransformer;
use Recurr\Transformer\Constraint\BetweenConstraint;
use Recurr\Transformer\TextTransformer;
use ReflectionClass;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationRuleParser;
use GetStream\StreamChat\Client as StreamClient;
use Illuminate\Support\Facades\Schema;

class TestController extends Controller
{
    public function index()
    {
        $sm = Schema::getConnection()->getDoctrineSchemaManager();
        $doctrineTable = $sm->listTableDetails('users');

        dd($doctrineTable);

//        $usersIDs = User::on()
//            ->where('company_id', '=', 9)
//            ->get('id')
//            ->map(function ($item) {
//                return $item->id;
//            })
//            ->toArray();
//
//        dd($usersIDs);

//        User::on()->where('id', '=', 102)->delete();

//        dd(123);
//        User::on()->where('id', '=', 102)->delete();

//        $tmp = HelperReward::getAwarded(null);
//        dd($tmp);
//        return view('emails.invite');

//        dd(Carbon::now(), Carbon::now()->timestamp, HelperNow::getCarbon());
        /*
        $dateStart = '2021-10-01 15:10:00';
        $rruleString = 'RRULE:FREQ=DAILY;COUNT=3;INTERVAL=2;WKST=MO';

        $rruleService = new RruleService($dateStart, $rruleString);
        try {
            $rruleService->generateEventsDates();
        } catch (InvalidWeekday $e) {
        } catch (\Exception $e) {
        }

        $eventsDateTime = $rruleService->getEventsDateTime();
        $eventsDate = $rruleService->getEventsDate();
        $eventsTimestamp = $rruleService->getEventsTimestamp();

        dd($eventsDateTime, $eventsDate, $eventsTimestamp);
        */

        /*
        $startDate = new \DateTime('2021-10-01 00:00:00');
        $endDate = new \DateTime('2022-10-01 15:10:00');
        $rule = new Rule($rruleString, new \DateTime('2021-10-01 15:10:00'));
        $transformer = new ArrayTransformer();
        $constraint = new BetweenConstraint($startDate, $endDate);
        $eventsDates = $transformer->transform($rule, $constraint);

        $eventDateArray = [];
        foreach ($eventsDates as $eventDate) {
            $eventDateArray[] = $eventDate->getStart();
        }

        dd($eventDateArray);
        */
    }
}
