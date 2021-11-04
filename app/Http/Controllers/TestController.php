<?php

namespace App\Http\Controllers;

use App\Constants\RewardConstant;
use App\Helpers\Helper;
use App\Helpers\HelperBuddies;
use App\Helpers\HelperChat;
use App\Helpers\HelperCompany;
use App\Helpers\HelperDateTime;
use App\Helpers\HelperFriend;
use App\Helpers\HelperMember;
use App\Helpers\HelperMigration;
use App\Helpers\HelperNotific;
use App\Helpers\HelperNow;
use App\Helpers\HelperReward;
use App\Helpers\HelperRole;
use App\Helpers\HelperShoogle;
use App\Helpers\HelperShoogleList;
use App\Helpers\HelperShoogleProfile;
use App\Helpers\HelperShoogleStatistic;
use App\Helpers\HelperShooglesViews;
use App\Helpers\HelperStream;
use App\Helpers\HelperUser;
use App\Helpers\HelperWellbeing;
use App\Models\Company;
use App\Models\Invite;
use App\Models\ModelHasRole;
use App\Models\Shoogle;
use App\Models\UserHasShoogle;
use App\Models\WellbeingScores;
use App\Repositories\TestRepository;
use App\Services\NotificClientService;
use App\Services\PasswordRecoveryService;
use App\Services\RruleService;
use App\Traits\CommunityLevelValueTrait;
use Carbon\Carbon;
use Database\Seeders\IconRewardsSeeder;
use DateInterval;
use DateTime;
use Illuminate\Http\Request;
use App\User;
use App\Constants\RoleConstant;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
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
use App\Facades\PasswordRecoveryFacade;

/**
 * Class TestController
 * @package App\Http\Controllers
 */
class TestController extends Controller
{
    use CommunityLevelValueTrait;

    public function index()
    {
        $a = [
            'social'        => 1,
            'physical'      => 1,
            'mental'        => 1,
            'economical'    => 1,
            'spiritual'     => 1,
            'emotional'     => 1,
            'intellectual'  => 1,
        ];

        $b = [
            'social'        => 2,
            'physical'      => 2,
            'mental'        => 2,
            'economical'    => 2,
            'spiritual'     => 2,
            'emotional'     => 2,
            'intellectual'  => 2,
        ];

        $c = [];

        foreach ($a as $key => $value) {
            $c[$key] = $value + $b[$key];
        }

        dd($c);

//        $from = '2021-10-08';
//        $to = '2021-10-08';
//        $userIDs = HelperWellbeing::getUniqueUserIDsPerPeriod($from, $to);
//        dd($userIDs);

//        $userIDs = [30, 60];
//        $periodBegin = '2021-09-20';
//        $periodEnd = '2021-10-07';
//
//        $tmp = $this->getValue($userIDs, $periodBegin, $periodEnd);
//        dd($tmp);



//        $test = HelperCompany::getArrayUserIds(0);
//        dd($test);

//        $data = 'tyGE9cSXdwDD6HjMx5sw0JziLRSaHzRd5NhwZZwd3RwANZGxnTeytTd4mRv4';
//        $front = '$2y$10$OxJyQT1M6bxbQxhJqgDvHO6KXZdnPHUt0u5fMEhWEDQygDoMRRUo6';

//        $token = bcrypt($data);
//                $token = Hash::make($data);
//                dd($token);

//        $tmp = Hash::check($front, $data);
//        dd($tmp);

//        $tmp = \App\Models\PasswordReset::on()->where('email', '=', 'fox3@gmail.com')->count();
//        dd($tmp);
//        $code = 19782;
//        $recoveryCode = User::on()->where('password_recovery_code', '=', $code)->get();
//        dd( count($recoveryCode) );



//        $tmp = HelperRole::getRoleByEmail('fox3@gmail.com');
//        $tmp = HelperRole::getRoleByEmail('superadmin@gmail.com');
//        $tmp = HelperRole::getRoleByEmail('admin@gmail.com');
//        dd($tmp);


//        $result = Hash::check('92597', '$2y$10$cRdoqE4ApeOxvxEKMtuc4.WXBoV1JZ.1TMhlAn2c.JekymnSUkKQ2');
//        dd($result);

//        $tmp = PasswordRecoveryService::getCode();
//        dd($tmp);

//        $keyExists = DB::select(
//            DB::raw(
//                "
//                SHOW KEYS
//        FROM users
//        WHERE Key_name='users_email_unique'
//        "
//            )
//        );
//
//        dd($keyExists[0]->Key_name);



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
