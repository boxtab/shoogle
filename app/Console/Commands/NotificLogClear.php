<?php

namespace App\Console\Commands;

use App\Models\UserHasShoogleLog;
use Illuminate\Console\Command;

class NotificLogClear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notific:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clearing the user_has_shoogle_log table';

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
     * Count the number of rows in the table and display.
     */
    private function printCountRow()
    {
        $countRow = UserHasShoogleLog::on()->count();
        $this->info("The users_has_shoogle_log table contains $countRow rows");
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->comment('Clearing the user_has_shoogle_log table');

        $this->printCountRow();
        UserHasShoogleLog::on()->delete();
        $this->printCountRow();

        return 0;
    }
}
