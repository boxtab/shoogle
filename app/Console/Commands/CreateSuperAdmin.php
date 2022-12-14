<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use App\Constants\RoleConstant;
use Illuminate\Support\Facades\DB;
use App\User;
use Spatie\Permission\Models\Role;

class CreateSuperAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shoogle:superadmin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a user with super admin rights';

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
     */
    public function handle()
    {
        $firstName = $this->ask('First Name?');
        $lastName = $this->ask('Last Name?');
        $email = $this->ask('Email?');
        $password = $this->secret('Password ?');

        $validator = Validator::make([
            'first_name'    => $firstName,
            'last_name'     => $lastName,
            'email'         => $email,
            'password'      => $password,
        ], [
            'first_name'    => ['required', 'min:2', 'max:255'],
            'last_name'     => ['min:2', 'max:255'],
            'email'         => ['required', 'email', 'min:6', 'max:255'],
            'password'      => ['required', 'min:6', 'max:64'],
        ]);

        if ($validator->fails()) {
            $this->info('User with super admin role not created. See error messages below:');

            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return 1;
        }

        DB::transaction( function () use ($firstName, $lastName, $email, $password) {
            $user = User::on()->updateOrCreate(['email' => $email],
                [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'password' => bcrypt($password),
                ]);

            $user->assignRole(RoleConstant::SUPER_ADMIN);

            $this->info('Superadmin account successfully created or updated.');
        });

        return 0;
    }
}
