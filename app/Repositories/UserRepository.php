<?php

namespace App\Repositories;

use App\Constants\RoleConstant;
use App\Helpers\Helper;
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
                return $query->where('company_id', $this->companyId);
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
        DB::transaction( function () use($user, $credentials) {
            $user->first_name = $credentials['firstName'];
            $user->last_name = $credentials['lastName'];
            $user->department_id = $credentials['departmentId'];
            $user->save();

            if (
                ( ! is_null($credentials['isAdminCompany']) )
                && ( Helper::getRole(Auth::id()) === RoleConstant::SUPER_ADMIN )
            ) {
                $roleName = $credentials['isAdminCompany'] ? RoleConstant::COMPANY_ADMIN : RoleConstant::USER;
                $roleId = (int)(Role::where('name', $roleName)->first()->id);

                DB::table('model_has_roles')
                    ->where('model_id', '=', (int)$user->id)
                    ->update(['role_id' => $roleId]);
            }
        });
    }
}
