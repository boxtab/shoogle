<?php

namespace Database\Seeders;

use App\Constants\RankConstant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rows = DB::table('ranks')->upsert([

            [
                'id' => RankConstant::NEWBIE_ID,
                'name' => RankConstant::NEWBIE_NAME,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'id' => RankConstant::ROOKIE_ID,
                'name' => RankConstant::ROOKIE_NAME,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'id' => RankConstant::INTERMEDIATE_ID,
                'name' => RankConstant::INTERMEDIATE_NAME,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'id' => RankConstant::EXPERIENCED_ID,
                'name' => RankConstant::EXPERIENCED_NAME,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'id' => RankConstant::EXPERT_ID,
                'name' => RankConstant::EXPERT_NAME,
                'created_at' => now(),
                'updated_at' => now(),
            ],


        ], ['id'], ['name', 'created_at', 'updated_at']);

        echo "Rows: $rows\n";
    }
}
