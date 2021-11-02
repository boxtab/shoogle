<?php

namespace App\Repositories;

use App\Constants\RoleConstant;
use App\Helpers\HelperAvatar;
use App\Helpers\HelperInvite;
use App\Models\Invite;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class AuthRepository
 * @package App\Repositories
 */
class AuthRepository extends Repositories
{
    /**
     * @var User
     */
    protected $model;

    /**
     * AuthRepository constructor.
     * @param User $model
     */
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * User creation.
     *
     * @param array $credentials
     * @param $invite
     * @return mixed
     */
    public function signup(array $credentials, $invite)
    {
        return DB::transaction( function () use ( $credentials, $invite ) {

            $user = User::withTrashed()
                ->where('email', '=', $credentials['email'])
                ->first();

            if ( ! is_null( $user ) ) {
                $user->restore();
                $user->update([
                    'company_id'        => $invite->companies_id,
                    'department_id'     => $invite->department_id,
                    'first_name'        => isset($credentials['firstName']) ? $credentials['firstName'] : null,
                    'last_name'         => isset($credentials['lastName']) ? $credentials['lastName'] : null,
                    'about'             => isset($credentials['about']) ? $credentials['about'] : null,
                    'email'             => $credentials['email'],
                    'email_verified_at' => null,
                    'password'          => bcrypt($credentials['password']),
                    'remember_token'    => null,
                    'avatar'            => null,
                    'rank'              => 1,
                    'profile_image'     => null,
                ]);
            } else {
                $user = User::on()->create([
                    'company_id'    => $invite->companies_id,
                    'department_id' => $invite->department_id,
                    'password'      => bcrypt($credentials['password']),
                    'email'         => $credentials['email'],
                    'first_name'    => isset($credentials['firstName']) ? $credentials['firstName'] : null,
                    'last_name'     => isset($credentials['lastName']) ? $credentials['lastName'] : null,
                    'about'         => isset($credentials['about']) ? $credentials['about'] : null,
                    'rank'          => 1,
                ]);
                $user->assignRole(RoleConstant::USER);
            }

            HelperInvite::useInvite($invite->id, $user->id);

            if ( ! empty( $credentials['profileImage'] ) ) {
                HelperAvatar::saveAvatar($credentials['profileImage'], $user);
            }

            return $user;
        });
    }
}
