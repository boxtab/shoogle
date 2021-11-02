<?php

namespace App\Console\Commands;

use App\Helpers\HelperNotific;
use App\Services\NotificClientService;
use Illuminate\Console\Command;

class NotificPush extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notific:push';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sending notifications';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     * @throws \Exception
     */
    public function handle()
    {
        $notificClientService = new NotificClientService();
        $countSendNotific = $notificClientService->run();
        $this->info("Sending notification(s) $countSendNotific");

        return 0;
    }
}
