<?php

namespace Database\Seeders;

use App\Constants\RankConstant;
use App\Constants\WellbeingCategoriesConstant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WellbeingCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rows = DB::table('wellbeing_categories')->upsert([

            [
                'id' => WellbeingCategoriesConstant::SOCIAL_ID,
                'name' => WellbeingCategoriesConstant::SOCIAL_NAME,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'id' => WellbeingCategoriesConstant::PHYSICAL_ID,
                'name' => WellbeingCategoriesConstant::PHYSICAL_NAME,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'id' => WellbeingCategoriesConstant::MENTAL_ID,
                'name' => WellbeingCategoriesConstant::MENTAL_NAME,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'id' => WellbeingCategoriesConstant::FINANCIAL_ID,
                'name' => WellbeingCategoriesConstant::FINANCIAL_NAME,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'id' => WellbeingCategoriesConstant::SPIRITUAL_ID,
                'name' => WellbeingCategoriesConstant::SPIRITUAL_NAME,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'id' => WellbeingCategoriesConstant::EMOTIONAL_ID,
                'name' => WellbeingCategoriesConstant::EMOTIONAL_NAME,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'id' => WellbeingCategoriesConstant::INTELLECTUAL_ID,
                'name' => WellbeingCategoriesConstant::INTELLECTUAL_NAME,
                'created_at' => now(),
                'updated_at' => now(),
            ],


        ], ['id'], ['name', 'created_at', 'updated_at']);

        echo "Rows: $rows\n";
    }
}
