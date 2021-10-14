<?php

namespace App\Http\Controllers;

use App\Helpers\HelperNotific;
use App\Helpers\HelperNow;
use App\Services\NotificClientService;
use App\Services\RruleService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TrialController extends Controller
{
    /**
     * @throws \Exception
     */
    public function index()
    {
        $notificClientService = new NotificClientService();
        $notificClientService->run();
    }
}
