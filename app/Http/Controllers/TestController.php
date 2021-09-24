<?php

namespace App\Http\Controllers;

use App\Constants\RewardConstant;
use App\Helpers\HelperShoogleList;
use App\Helpers\HelperShoogleProfile;
use App\Helpers\HelperShoogleStatistic;
use App\Helpers\HelperShooglesViews;
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
        HelperShoogleProfile::getShooglesByUserID(30);

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
