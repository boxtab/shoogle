<?php

namespace Database\Seeders;

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    /**
     * The amount of credentials to create a test user.
     */
    const QUANTITY_CREDENTIALS = 3;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $countCredentials = 0;

        if ( config('app.TEST_USER_NAME') === null ) {
            echo 'Warning: The .env file does not have a name for the test user' . PHP_EOL;
        } else {
            $countCredentials++;
        }

        if ( config('app.TEST_USER_EMAIL') === null ) {
            echo 'Warning: No email set for test user in .env file' . PHP_EOL;
        } else {
            $countCredentials++;
        }

        if ( config('app.TEST_USER_PASSWORD') === null ) {
            echo 'Warning: There is no password in the environment file for the test user.' . PHP_EOL;
        } else {
            $countCredentials++;
        }

        if ( $countCredentials === self::QUANTITY_CREDENTIALS ) {
            User::updateOrCreate(['email' => config('app.TEST_USER_EMAIL')],
                [
                    'first_name' => config('app.TEST_USER_NAME'),
                    'email' => config('app.TEST_USER_EMAIL'),
                    'password' => bcrypt( config('app.TEST_USER_PASSWORD') ),
//                    'password' => Hash::make( config('app.TEST_USER_PASSWORD') ),
                ]);
            echo 'Test user created successfully!' . PHP_EOL;
        } else {
            echo 'Error: No test user has been created. Update your .env file.' . PHP_EOL;
        }
    }
}
