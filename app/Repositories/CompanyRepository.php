<?php

namespace App\Repositories;

use App\Constants\RoleConstant;
use App\Helpers\Helper;
use App\Models\Company;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class CompanyRepository
 * @package App\Repositories
 */
class CompanyRepository extends Repositories
{
    /**
     * @var Company
     */
    protected $model;

    /**
     * CompanyRepository constructor.
     * @param Company $model
     */
    public function __construct(Company $model)
    {
        parent::__construct($model);
    }

    /**
     * Get a list of companies.
     *
     * @return array
     */
    public function getList(): array
    {
        return DB::select(DB::raw('
                select
                    c.id as id,
                    c.name as company_name,
                    (
                        select
                            un.first_name
                        from users as un
                        left outer join model_has_roles as mhr on un.id = mhr.model_id
                        left outer join roles as r on r.id = mhr.role_id
                        where un.company_id = c.id
                          and r.name = "company-admin"
                        limit 1
                    ) as contact_person_first_name,
                    (
                        select
                            ul.last_name
                        from users as ul
                        left outer join model_has_roles as mhrl on ul.id = mhrl.model_id
                        left outer join roles as r on r.id = mhrl.role_id
                        where ul.company_id = c.id
                          and r.name = "company-admin"
                        limit 1
                    ) as contact_person_last_name,
                    (
                        select
                            un.email
                        from users as un
                        left outer join model_has_roles as mhr on un.id = mhr.model_id
                        left outer join roles as r on r.id = mhr.role_id
                        where un.company_id = c.id
                          and r.name = "company-admin"
                        limit 1
                    ) as contact_person_email,
                    (select count(uc.id) from users as uc where uc.company_id = c.id) as users_count
                from companies as c
                order by c.id
            '));
    }

    /**
     * Get the entity of the admin company by the company ID.
     *
     * @param int $companyId
     * @return \Illuminate\Database\Eloquent\Builder|Model
     */
    public function getAdminByCompanyId(int $companyId): User
    {
        return User::on()->
            leftJoin('model_has_roles', function($join) {
                $join->on('users.id', '=', 'model_has_roles.model_id');
            })->leftJoin('roles', function($join) {
                $join->on('roles.id', '=', 'model_has_roles.role_id');
            })
            ->where('users.company_id', $companyId)
            ->where('roles.name', RoleConstant::COMPANY_ADMIN)
            ->firstOrFail();
    }

    /**
     * Create a new company.
     *
     * @param array $credentials
     */
    public function create(array $credentials): void
    {
        DB::transaction( function () use ($credentials) {

            $company = Company::create([
                'name' => $credentials['companyName'],
            ]);

            $user = User::create([
                'company_id'    => $company->id,
                'first_name'    => $credentials['firstName'],
                'last_name'     => $credentials['lastName'],
                'email'         => $credentials['email'],
                'password'      => bcrypt($credentials['password']),
            ]);

            $user->assignRole(RoleConstant::COMPANY_ADMIN);

        });
    }

    /**
     * Changes to company information and administrator credentials of that company.
     *
     * @param Company $company
     * @param array $credentials
     */
    public function update(Company $company, array $credentials): void
    {
        DB::transaction( function () use ($company, $credentials) {

            $company->update(['name' => $credentials['companyName']]);

            $userAdminCompany = User::on()->
                leftJoin('model_has_roles', function($join) {
                    $join->on('users.id', '=', 'model_has_roles.model_id');
                })->leftJoin('roles', function($join) {
                    $join->on('roles.id', '=', 'model_has_roles.role_id');
                })
                ->where('users.company_id', $company->id)
                ->where('roles.name', RoleConstant::COMPANY_ADMIN)
                ->firstOrFail(['users.id']);

            $userAdminCompany->update([
                'company_id'    => $company->id,
                'first_name'    => $credentials['firstName'],
                'last_name'     => $credentials['lastName'],
                'email'         => $credentials['email'],
                'password'      => bcrypt($credentials['password']),
            ]);
        });
    }

    /**
     * Deleting a company.
     *
     * @param Company $company
     */
    public function destroy(Company $company): void
    {
        DB::transaction( function () use ($company) {
            $user = User::where('company_id', $company->id)->first();
            $user->roles()->detach();

            User::where('company_id', $company->id)->delete();
            Company::where('id', $company->id)->delete();
        });
    }
}
