<?php

namespace Database\Seeders;

use App\Constants\NotificationsTypeConstant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationsTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rows = DB::table('notifications_type')->upsert([

            [
                'id' => NotificationsTypeConstant::SHOOGLE_REMIDER_ID,
                'name' => NotificationsTypeConstant::SHOOGLE_REMAINDER_NAME,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => NotificationsTypeConstant::BUDDY_REQUEST_ID,
                'name' => NotificationsTypeConstant::BUDDY_REQUEST_NAME,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => NotificationsTypeConstant::BUDDY_CONFIRM_ID,
                'name' => NotificationsTypeConstant::BUDDY_CONFIRM_NAME,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => NotificationsTypeConstant::BUDDY_REJECT_ID,
                'name' => NotificationsTypeConstant::BUDDY_REJECT_NAME,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => NotificationsTypeConstant::BUDDY_DISCONNECT_ID,
                'name' => NotificationsTypeConstant::BUDDY_DISCONNECT_NAME,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => NotificationsTypeConstant::REWARD_ASSIGN_ID,
                'name' => NotificationsTypeConstant::REWARD_ASSIGN_NAME,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => NotificationsTypeConstant::WELLBEING_REMIDER_ID,
                'name' => NotificationsTypeConstant::WELLBEING_REMINDER_NAME,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'id' => NotificationsTypeConstant::RANK_ASSIGN_ID,
                'name' => NotificationsTypeConstant::RANK_ASSIGN_NAME,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'id' => NotificationsTypeConstant::ACCESS_DENIED_ID,
                'name' => NotificationsTypeConstant::ACCESS_DENIED_NAME,
                'created_at' => now(),
                'updated_at' => now(),
            ],


        ], ['id'], ['name', 'created_at', 'updated_at']);

        echo "Rows: $rows\n";
    }
}
