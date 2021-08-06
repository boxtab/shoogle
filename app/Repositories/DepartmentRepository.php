<?php

namespace App\Repositories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DepartmentRepository extends Repositories
{
    /**
     * @var Department
     */
    protected $model;

    /**
     * DepartmentRepository constructor.
     *
     * @param Department $model
     */
    public function __construct(Department $model)
    {
        parent::__construct($model);
    }

    /**
     * List of departments with the number of employees.
     *
     * @return mixed
     */
    public function getList()
    {
        $companyId = getCompanyIdFromJWT();

        return $this->model
            ->select(DB::raw('
                departments.id as department_id,
                departments.name as department_name,
                count(users.id) as shooglers'))
            ->leftJoin('users', 'users.department_id', '=', 'departments.id')
            ->when( ! is_null($companyId), function($query) use ($companyId) {
                return $query->where('departments.company_id', $companyId);
            })
            ->groupBy('departments.id', 'departments.name')
            ->get();
    }

    /**
     * Detailed information on the department.
     *
     * @param int $id
     * @return mixed
     */
    public function getDetail(int $id)
    {
        return $this->model->where('id', $id)->get();
    }

    /**
     * Массив департаментов для текущего пользователя.
     *
     * @return mixed
     */
    public function getItems()
    {
//        get()
//            ->map( function ( $item ) {
//                return [ 'id' => $item->id, 'name' => $item->name ];
//            })->toArray();

        $companyId = getCompanyIdFromJWT();

        return $this->where('company_id', $companyId)
            ->get('name')
            ->map(function ($item) {
                return $item->name;
            })
            ->toArray();
    }
}
