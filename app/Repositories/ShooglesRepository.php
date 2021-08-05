<?php

namespace App\Repositories;

use App\Models\Shoogle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShooglesRepository extends Repositories
{
    /**
     * @var Shoogle
     */
    protected $model;

    /**
     * ShoogleRepository constructor.
     *
     * @param Shoogle $model
     */
    public function __construct(Shoogle $model)
    {
        parent::__construct($model);
    }

    /**
     * List of shoogles.
     *
     * @param string $search
     * @return mixed
     */
    public function getList(string $search)
    {
//        $companyId = getCompanyIdFromJWT();

        return Shoogle::on()
            ->leftJoin('users', 'users.id', '=', 'shoogles.owner_id')
            ->leftJoin('departments', 'users.department_id', '=', 'departments.id')
//            ->when( ! is_null($companyId), function($query) use ($companyId) {
//                return $query->where('users.company_id', $companyId);
//            })
            ->when( ! is_null( $search ) , function ($query) use ($search) {
                return $query->where('title', 'like', '%' . $search .'%');
            })
            ->get();
    }
}

