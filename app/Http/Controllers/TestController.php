<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Invite;
use App\Models\Shoogle;
use App\Models\WellbeingScores;
use App\Repositories\TestRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\User;
use App\Constants\RoleConstant;
use Recurr\Rule;
use Recurr\Transformer\TextTransformer;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationRuleParser;

class TestController extends Controller
{
    public function index()
    {
        null;

        $sample = "RRULE:FREQ=WEEKLY;COUNT=30;INTERVAL=1;WKST=MO";

        $rule = new Rule($sample, new \DateTime());
        $textTransformer = new TextTransformer();
        echo $textTransformer->transform($rule);

//        $shoogle = Shoogle::find(11);
//        $shoogle->reminder_interval = 'kk';
//        $shoogle->save();
//        $output = $shoogle->reminder_interval;
//        return $output;
    }
}
