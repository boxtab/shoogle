<?php

namespace Database\Seeders;

use App\Constants\RoleConstant;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    /**
     * The number of credentials to create a super admin user.
     */
    const QUANTITY_SUPER_ADMIN_CREDENTIALS = 4;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $countCredentials = 0;

        if ( env('SUPER_ADMIN_NAME') === null ) {
            echo 'Warning: There is no username in the .env file for the super admin user.' . PHP_EOL;
        } else {
            $countCredentials++;
        }

        if ( env('SUPER_ADMIN_EMAIL') === null ) {
            echo 'Warning: In the .env file, the email address is not specified for the super admin user.' . PHP_EOL;
        } else {
            $countCredentials++;
        }

        if ( env('SUPER_ADMIN_PASSWORD') === null ) {
            echo 'Warning: There is no password in the environment file for the super admin user.' . PHP_EOL;
        } else {
            $countCredentials++;
        }

        if ( ! Role::findByName(RoleConstant::SUPER_ADMIN) ) {
            echo 'Warning: Role table has no role for superadmin.' . PHP_EOL;
        } else {
            $countCredentials++;
        }

        if ( $countCredentials === self::QUANTITY_SUPER_ADMIN_CREDENTIALS ) {

            DB::transaction( function () {
                $user = User::updateOrCreate(['email' => env('SUPER_ADMIN_EMAIL')],
                    [
                        'name' => env('SUPER_ADMIN_NAME'),
                        'email' => env('SUPER_ADMIN_EMAIL'),
                        'password' => bcrypt(env('SUPER_ADMIN_PASSWORD')),
                    ]);

                $user->assignRole(RoleConstant::SUPER_ADMIN);
                echo 'Super admin user created successfully!' . PHP_EOL;
            });

        } else {
            echo 'Error: Super admin user has not been created. Update your .env file.' . PHP_EOL;
        }
    }
}
