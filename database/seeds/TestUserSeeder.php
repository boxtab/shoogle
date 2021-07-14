<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rows = DB::table('users')->insertOrIgnore([
            'name' => 'testuser',
            'email' => 'testuser@gmail.com',
            'password' => Hash::make('secret'),
        ]);

        echo "Rows: $rows" . PHP_EOL;
    }
}
