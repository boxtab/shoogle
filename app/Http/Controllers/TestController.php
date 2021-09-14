<?php

namespace App\Http\Controllers;

use App\Enums\ShooglerEnum;
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
        $collection = collect([
            ['id'=>1, 'name'=>'Hardik', 'city' => 'Mumbai', 'country' => 'India'],
            ['id'=>2, 'name'=>'Vimal', 'city' => 'New York', 'country' => 'US'],
            ['id'=>3, 'name'=>'Harshad', 'city' => 'Gujarat', 'country' => 'India'],
            ['id'=>4, 'name'=>'Harsukh', 'city' => 'New York', 'country' => 'US'],
        ]);

        $grouped = $collection->groupBy(function ($item, $key) {
            return $item['country'].$item['city'];
        });

        dd($grouped);

//        null;
//        phpinfo();
    }
}
