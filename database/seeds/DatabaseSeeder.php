<?php

use Database\Seeders\GuardApiSeeder;
use Database\Seeders\IconRewardsSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SuperAdminSeeder;
use Database\Seeders\AdminSeeder;
use Database\Seeders\TestUserSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(IconRewardsSeeder::class);
        $this->call(TestUserSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(GuardApiSeeder::class);
        $this->call(SuperAdminSeeder::class);
        $this->call(AdminSeeder::class);
    }
}
