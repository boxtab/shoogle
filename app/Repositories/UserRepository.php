<?php

namespace App\Repositories;

use App\Constants\RoleConstant;
use App\Helpers\Helper;
use App\Models\Invite;
use App\Models\ModelHasRole;
use App\Models\Role;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

/**
 * Class UserRepository
 * @package App\Repositories
 */
class UserRepository extends Repositories
{
    /**
     * @var User
     */
    protected $model;

    /**
     * UserRepository constructor.
     *
     * @param User $model
     */
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * List of users.
     *
     * @return mixed
     */
    public function getList()
    {
        return $this->model->on()
            ->when( ! $this->noCompany() , function ($query) {
                return $query->where('company_id', '=', $this->companyId);
            })
            ->get();
    }

    /**
     * Create new user.
     *
     * @param array $credentials
     */
    public function create(array $credentials): void
    {
        DB::transaction( function () use($credentials) {
            $user = new User();
            $user->company_id = $this->companyId;
            $user->department_id = $credentials['departmentId'];
            $user->first_name = $credentials['firstName'];
            $user->last_name = $credentials['lastName'];
            $user->email = $credentials['email'];
            $user->save();

            $user->assignRole(RoleConstant::USER);
        });
    }

    /**
     * Update a user.
     *
     * @param User $user
     * @param array $credentials
     */
    public function update(User $user, array $credentials): void
    {
        $user->first_name = $credentials['firstName'];
        $user->last_name = $credentials['lastName'];

        if ( ( ! is_null( $credentials['email'] ) ) && ( $credentials['email'] !== $user->email ) ) {
            if ( User::on()->where('email', '=', $credentials['email'])->count() === 0 ) {
                $user->email = $credentials['email'];
            }
        }

        $user->department_id = $credentials['departmentId'];
        $user->save();

        if (
            ( ! is_null($credentials['isCompanyAdmin']) )
            && ( Helper::getRole(Auth::id()) === RoleConstant::SUPER_ADMIN )
        ) {
            $roleName = $credentials['isCompanyAdmin'] ? RoleConstant::COMPANY_ADMIN : RoleConstant::USER;
            $roleId = (int)(Role::on()->where('name', '=', $roleName)->first()->id);


            $countModelHasRole = ModelHasRole::on()
                ->where('model_id', '=', (int)$user->id)
                ->count();

            if ( $countModelHasRole === 0 ) {
                DB::table('model_has_roles')->insert([
                    'role_id' => $roleId,
                    'model_type' => 'App\User',
                    'model_id' => $user->id,
                ]);
            } else {
                DB::table('model_has_roles')
                    ->where('model_id', '=', (int)$user->id)
                    ->update(['role_id' => $roleId]);
            }
        }
    }

    /**
     * Delete user by ID.
     *
     * @param int $userId
     */
    public function delete(int $userId)
    {
        DB::transaction( function () use ($userId) {
            Invite::on()->where('user_id', '=', $userId)->delete();
            User::on()->where('id', '=', $userId)->delete();
        });
    }
}
