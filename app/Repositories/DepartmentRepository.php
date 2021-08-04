<?php

namespace App\Repositories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

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

    public function getList()
    {
        return $this->model->get(['id', 'name']);
    }
}
