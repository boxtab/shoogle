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
        //Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        Role::create(['name' => RoleConstant::SUPER_ADMIN]);
        Role::create(['name' => RoleConstant::COMPANY_ADMIN]);
        Role::create(['name' => RoleConstant::USER]);

        $user = User::first();
        $user->assignRole(RoleConstant::SUPER_ADMIN);
    }
}
