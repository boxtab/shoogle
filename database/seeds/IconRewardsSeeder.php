<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IconRewardsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rows = DB::table('rewards')->insertOrIgnore([
            ['id' => 1, 'name' => 'ApplePie', 'icon' => 'ApplePie.png', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'BlessUp', 'icon' => 'BlessUp.png', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'Facepalm', 'icon' => 'Facepalm.png', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'name' => 'HealthcareHero', 'icon' => 'HealthcareHero.png', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'name' => 'HotDog', 'icon' => 'HotDog.png', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'name' => 'NarwhalSalute', 'icon' => 'NarwhalSalute.png', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'name' => 'TreeHug', 'icon' => 'TreeHug.png', 'created_at' => now(), 'updated_at' => now()],
        ]);

        echo "Rows: $rows\n";
    }
}
