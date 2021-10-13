<?php

namespace App\Http\Controllers;

use App\Helpers\HelperNotific;
use App\Services\NotificClientService;
use App\Services\RruleService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TrialController extends Controller
{
    public function index()
    {
        $dateStart = '2021-10-01 15:10:00';

        $dateMidnight = (new \DateTime($dateStart))->format('Y-m-d') . ' 00:00:00';
        $datePlusYear = date('Y-m-d H:i:s', strtotime('+1 year', strtotime($dateStart)));
        dd($dateStart, $dateMidnight, $datePlusYear);
//        $notificClientService = new NotificClientService();
//        $notificClientService->run();
    }
}
