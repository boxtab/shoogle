<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Invite;
use App\Models\Shoogle;
use App\Models\UserRanks;
use App\Models\WellbeingScores;
use App\Repositories\TestRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\User;
use App\Constants\RoleConstant;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class TestController extends Controller
{
    public function index()
    {
        null;

        $fieldCredentials = [
            'company_id'    => 123,
            'first_name'    => 'abc',
        ];

        $fieldCredentials['password'] = '123qwe+++';

        dd($fieldCredentials);


//        $userRank = new UserRanks();
//        $userRank->user_id = 3;
//        $userRank->rank = 2;
//        $userRank->save();

//        echo Auth::user()->id;
//        $welbeingScores = new WellbeingScores();
//        $welbeingScores->user_id = 3;
//
//        $welbeingScores->social = 5;
//        $welbeingScores->physical = 6;
//        $welbeingScores->mental = 7;
//        $welbeingScores->economical = 8;
//        $welbeingScores->spiritual = 9;
//        $welbeingScores->emotional = 10;
//        $welbeingScores->save();
    }
}
