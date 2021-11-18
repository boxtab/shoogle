<?php

namespace Database\Seeders;

use App\Constants\RewardConstant;
use App\Models\Reward;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

        $path = public_path(RewardConstant::PATH);
        $files = scandir($path);
        $files = array_values( array_diff($files, ['.', '..']) );

        for ($i = 0; $i < count($files); $i++) {
            $reward = [
                'id' => $i + 1,
                'name' => ucfirst( str_replace( '_', ' ', pathinfo($files[$i], PATHINFO_FILENAME) ) ),
                'icon' => $files[$i],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
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
        $rows = DB::table('rewards')
            ->upsert( $this->getRewards(), ['id', 'name', 'icon'], ['created_at', 'updated_at'] );

        echo "Rows: $rows\n";
    }
}
