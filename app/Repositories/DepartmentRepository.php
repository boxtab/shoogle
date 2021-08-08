<?php

namespace App\Repositories;

use App\Helpers\Helper;
use App\Models\Department;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class DepartmentRepository
 * @package App\Repositories
 */
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
            ->select(DB::raw('
                departments.id as department_id,
                departments.name as department_name,
                count(users.id) as shooglers'))
            ->leftJoin('users', 'users.department_id', '=', 'departments.id')
            ->when( ! $this->noCompany(), function($query) {
                return $query->where('departments.company_id', $this->companyId);
            })
            ->groupBy('departments.id', 'departments.name')
            ->get();
    }

    /**
     * Create department.
     *
     * @param string $departmentName
     * @throws \Exception
     */
    public function createDepartment(string $departmentName)
    {
        if ( $this->noCompany() ) {
            throw new \Exception('No company selected', Response::HTTP_FAILED_DEPENDENCY);
        }

        $this->create([
            'company_id' => $this->companyId,
            'name' => $departmentName,
        ]);
    }
}
