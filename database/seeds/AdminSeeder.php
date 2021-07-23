<?php

namespace Database\Seeders;

use App\Constants\RoleConstant;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * The number of credentials to create a admin user.
     */
    const QUANTITY_ADMIN_CREDENTIALS = 4;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $countCredentials = 0;

        if ( env('ADMIN_NAME') === null ) {
            echo 'Warning: There is no username in the .env file for the admin user.' . PHP_EOL;
        } else {
            $countCredentials++;
        }

        if ( env('ADMIN_EMAIL') === null ) {
            echo 'Warning: In the .env file, the email address is not specified for the admin user.' . PHP_EOL;
        } else {
            $countCredentials++;
        }

        if ( env('ADMIN_PASSWORD') === null ) {
            echo 'Warning: There is no password in the environment file for the admin user.' . PHP_EOL;
        } else {
            $countCredentials++;
        }

        if ( ! Role::findByName(RoleConstant::COMPANY_ADMIN) ) {
            echo 'Warning: Role table has no role for admin.' . PHP_EOL;
        } else {
            $countCredentials++;
        }

        if ( $countCredentials === self::QUANTITY_ADMIN_CREDENTIALS ) {

            DB::transaction( function () {
                $user = User::updateOrCreate(['email' => env('ADMIN_EMAIL')],
                    [
                        'first_name' => env('ADMIN_NAME'),
                        'email' => env('ADMIN_EMAIL'),
                        'password' => bcrypt(env('ADMIN_PASSWORD')),
                    ]);

                $user->assignRole(RoleConstant::COMPANY_ADMIN);
                echo 'Admin user created successfully!' . PHP_EOL;
            });

        } else {
            echo 'Error: Admin user has not been created. Update your .env file.' . PHP_EOL;
        }
    }
}
