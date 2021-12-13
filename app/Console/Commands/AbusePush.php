<?php

namespace App\Console\Commands;

use App\Helpers\HelperAbuse;
use Illuminate\Console\Command;
use Exception;

class AbusePush extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'abuse:push';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Distribution of letters with complaints.';

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
     * @throws Exception
     */
    public function handle()
    {
        $this->info( $this->description );

        try {
            HelperAbuse::send();
        } catch (Exception $e) {
            $this->error($e->getMessage());
            return Command::INVALID;
        }

        return Command::SUCCESS;
    }
}
