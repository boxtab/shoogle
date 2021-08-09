<?php

namespace App\Repositories;

use App\Constants\RoleConstant;
use App\Helpers\Helper;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        return User::on()
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
}
