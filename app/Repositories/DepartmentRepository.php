<?php

namespace App\Repositories;

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
     * Array of departments for the current user.
     *
     * @return mixed
     */
    public function getItems()
    {
        $companyId = getCompanyIdFromJWT();

        if ( is_null($companyId) ) {
            return [];
        } else {
            return $this->where('company_id', $companyId)->get();
        }
    }

    /**
     * Create department.
     *
     * @param string $departmentName
     * @throws \Exception
     */
    public function createDepartment(string $departmentName)
    {
        $companyId = getCompanyIdFromJWT();

        if ( is_null( $companyId ) ) {
            throw new \Exception('No company selected', Response::HTTP_FAILED_DEPENDENCY);
        }

        $this->create([
            'company_id' => $companyId,
            'name' => $departmentName,
        ]);
    }
}
