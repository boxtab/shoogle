<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Constants\TestUserConstant;

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
            'name' => TestUserConstant::NAME,
            'email' => TestUserConstant::EMAIL,
            'password' => Hash::make(TestUserConstant::PASSWORD),
        ]);

        echo "Rows: $rows" . PHP_EOL;
    }
}
