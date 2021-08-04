<?php

namespace App\Repositories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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
        return $this->model
            ->select(DB::raw('departments.name as department_name, count(users.id) as shooglers'))
            ->leftJoin('users', 'users.department_id', '=', 'departments.id')
            ->groupBy('departments.name')
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
}
