<?php

use Database\Seeders\RoleSeeder;
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
//         $this->call(UserSeeder::class);
         $this->call(TestUserSeeder::class);
         $this->call(RoleSeeder::class);
    }
}
