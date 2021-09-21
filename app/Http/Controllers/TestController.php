<?php

namespace App\Http\Controllers;

use App\Helpers\HelperShoogleList;
use App\Helpers\HelperShoogleStatistic;
use App\Helpers\HelperShooglesViews;
use App\Models\Company;
use App\Models\Invite;
use App\Models\ModelHasRole;
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
//        $tmp = HelperShooglesViews::getLastActivityBy(2);
        return date('Y-m-d H:i:s');
//        dd($tmp);
//        null;
//        phpinfo();
    }
}
