<?php

namespace App\Http\Controllers;

use App\Enums\ShooglerEnum;
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
use ReflectionClass;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationRuleParser;

class TestController extends Controller
{
    public function index()
    {
        null;

        return ReflectionClass::getConstants('ShooglerEnum');

//        $shoogle = Shoogle::find(11);
//        $shoogle->reminder_interval = 'kk';
//        $shoogle->save();
//        $output = $shoogle->reminder_interval;
//        return $output;
    }
}
