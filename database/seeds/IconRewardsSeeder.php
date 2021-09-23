<?php

namespace Database\Seeders;

use App\Constants\RewardConstant;
use App\Models\Reward;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class IconRewardsSeeder extends Seeder
{
    /**
     * Clear the table from old icons.
     */
    private function clearOldIcons()
    {
        $oldIcon = [
            'ApplePie.png',
            'BlessUp.png',
            'Facepalm.png',
            'HealthcareHero.png',
            'HotDog.png',
            'NarwhalSalute.png',
            'TreeHug.png'
        ];

        Reward::on()->whereIn('icon', $oldIcon)->delete();
    }

    /**
     * Reads icon files and returns a prepared array for writing.
     *
     * @return array
     */
    private function getRewards()
    {
        $rewards = [];
        $files = Storage::disk('public')->files(RewardConstant::PATH);
        for ($i = 0; $i < count($files); $i++) {
            $reward = [
                'id' => $i + 1,
                'name' => ucfirst( str_replace( '_', ' ', pathinfo($files[$i], PATHINFO_FILENAME) ) ),
                'icon' => substr($files[$i], strlen(RewardConstant::PATH . '/')),
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $rewards[] = $reward;
        }
        return $rewards;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rows = DB::table('rewards')->upsert( $this->getRewards(), 'id' );
        echo "Rows: $rows\n";

        /*
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
        */
    }
}
