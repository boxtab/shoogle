<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Constants\RoleConstant;
use App\User;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        Role::updateOrCreate(['name' => RoleConstant::SUPER_ADMIN, 'guard_name' => 'api']);
        Role::updateOrCreate(['name' => RoleConstant::COMPANY_ADMIN, 'guard_name' => 'api']);
        Role::updateOrCreate(['name' => RoleConstant::USER, 'guard_name' => 'api']);

        /*
        if ( ! Role::findByName(RoleConstant::SUPER_ADMIN) ) {
            Role::create(['name' => RoleConstant::SUPER_ADMIN]);
        }

        if ( ! Role::findByName(RoleConstant::COMPANY_ADMIN) ) {
            Role::create(['name' => RoleConstant::COMPANY_ADMIN]);
        }

        if ( ! Role::findByName(RoleConstant::USER) ) {
            Role::create(['name' => RoleConstant::USER]);
        }
        */
        if ( env('TEST_USER_EMAIL') !== null ) {
            $user = User::where('email', env('TEST_USER_EMAIL'))->first();
            $user->assignRole(RoleConstant::USER);
        } else {
            echo 'Error: No email set for test user in .env file' . PHP_EOL;
        }
    }
}
