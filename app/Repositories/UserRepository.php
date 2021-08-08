<?php

namespace App\Repositories;

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
        $companyId = Helper::getCompanyIdFromJWT();

        return User::on()
            ->when( ! is_null( $companyId ) , function ($query) use ($companyId) {
                return $query->where('company_id', $companyId);
            })
            ->get();
    }

}
