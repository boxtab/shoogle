<?php

namespace App\Console\Commands;

use App\Services\WellbeingService;
use Illuminate\Console\Command;

class WellbeingPush extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wellbeing:push';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Wellbeing points reminders';

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
     * @throws \GetStream\StreamChat\StreamException
     */
    public function handle()
    {
        $this->info( $this->description );

        $wellbeingService = new WellbeingService();
        $countSendNotific = $wellbeingService->run();
        $this->info("$countSendNotific notification(s) sent");

        return Command::SUCCESS;
    }
}
