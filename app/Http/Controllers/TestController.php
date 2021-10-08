<?php

namespace App\Http\Controllers;

use App\Constants\RewardConstant;
use App\Helpers\HelperBuddies;
use App\Helpers\HelperChat;
use App\Helpers\HelperFriend;
use App\Helpers\HelperMember;
use App\Helpers\HelperNotific;
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
use App\Models\WellbeingScores;
use App\Repositories\TestRepository;
use Carbon\Carbon;
use Database\Seeders\IconRewardsSeeder;
use Illuminate\Http\Request;
use App\User;
use App\Constants\RoleConstant;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Recurr\Exception\InvalidRRule;
use Recurr\Rule;
use Recurr\Transformer\ArrayTransformer;
use Recurr\Transformer\Constraint\BetweenConstraint;
use Recurr\Transformer\TextTransformer;
use ReflectionClass;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationRuleParser;
use GetStream\StreamChat\Client as StreamClient;

class TestController extends Controller
{
    public function index()
    {
        $rruleString = 'RRULE:FREQ=WEEKLY;COUNT=30;INTERVAL=1;WKST=MO';

        // определяешь граничные даты, чтобы не получать лишние даты.
        // потому что RRuleв теории может вернуть дат на 10 лет вперед
        $startDate = new \DateTime('today midnight');
        $endDate = new \DateTime('today +1 years 23:59:59');


        // скармливаешь строку "RRULE:... " либе
        try {
            $rule = new Rule($rruleString, new \DateTime('today midnight'));
        } catch (InvalidRRule $e) {
        }

        // так надо :-)

        $transformer = new ArrayTransformer();
        $constraint = new BetweenConstraint($startDate, $endDate);

        // тут уже будет массив объектов, в которые должно происходить событие.
        // Объекты, кажется специфические, но из каждого можно получить метку времени.
        $eventsDates = $transformer->transform($rule, $constraint);

        dd($eventsDates);

        /*
        foreach ($eventsDates as $eventDate) {
            $startDate = $eventDate->getStart();
        }
        */

//        HelperNotific::push(60, 60, 53);
//        $tmp = HelperBuddies::isFriends(51, 3, 60);
//
//        if ( is_null($tmp) ) {
//            return 'is null true';
//        }
//
//        if ( $tmp == true ) {
//            return 'is true';
//        }
//
//        if ( $tmp == false ) {
//            return 'is false';
//        }
//
//        return $tmp;

//        $tmp = HelperChat::getBuddyChatId(51, 60);
//
//        if ( is_null($tmp) ) {
//            return 'null - ';
//        } else {
//            return $tmp;
//        }

//        new HelperStream();
//        dd(111);
//        HelperStream::init();

//        $shoogleId = 1;
//        $idOfFirstUser = 2;
//        $idOfSecondUser = 3;
//        $serverClient = new StreamClient(config('stream.stream_api_key'), config('stream.stream_api_secret'));
//        $newChannel = $serverClient->Channel('messaging', 'shoogle'.$shoogleId.'Buddy'.$idOfFirstUser.'with'.$idOfSecondUser);
//        $newChannel->create(Auth::id(), [$idOfFirstUser, $idOfSecondUser]);
//        return $newChannel->id;

        //        dd($tmp);

        /*
        $streamApiKey = 'fms7mkz25hdf';
        $streamApiSecret = 'y8tm6k35avd35txgy4jn27tfagz2yjgxxfwuwrxzm4c43895ehrtz4uh53gqvz5r';

        $server_client = new StreamClient($streamApiKey, $streamApiSecret);
        $token = $server_client->createToken("john");

        dd($token);
        */


//        $tmp = new IconRewardsSeeder();
//        $tmp2 = $tmp->getRewards();
//        dd($tmp2);

/*
        $rewards = [];
        $path = public_path(RewardConstant::PATH);
        $files = scandir($path);
        $files = array_values( array_diff($files, ['.', '..']) );
        dd($files);

        for ($i = 0; $i < count($files); $i++) {
            $reward = [
                'id' => $i + 1,
                'name' => ucfirst( str_replace( '_', ' ', pathinfo($files[$i], PATHINFO_FILENAME) ) ),
                'icon' => $files[$i],
//                'icon' => substr($files[$i], strlen(RewardConstant::PATH . '/')),
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $rewards[] = $reward;
        }
        dd($rewards);
*/
    }
}
