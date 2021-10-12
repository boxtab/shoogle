<?php

namespace App\Http\Controllers;

use App\Services\RruleService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TrialController extends Controller
{
    public function index()
    {
//        $income = Carbon::now();
        $income = '2021-10-11 16:40:00';

        $tmp = gettype($income);

        dd($tmp);

        /*
        $dateStart = '2021-10-11 16:40:00';
        $rruleString = 'RRULE:FREQ=DAILY;COUNT=3;INTERVAL=2;WKST=MO';

        $rruleService = new RruleService($dateStart, $rruleString);
        $rruleService->generateEventsDates();
        $eventsDate = $rruleService->getEventsDatesDatetime();

        dd($eventsDate);

        return 123;
        */
    }
}
