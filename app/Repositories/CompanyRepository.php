<?php

namespace App\Repositories;

use App\Constants\RoleConstant;
use App\Helpers\Helper;
use App\Models\Company;
use App\Models\Invite;
use App\Traits\CompanyTrait;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use \Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class CompanyRepository
 * @package App\Repositories
 */
class CompanyRepository extends Repositories
{
    use CompanyTrait;
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
     * @param string $order
     * @return array
     */
    public function getList(string $order): array
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
                    (select count(uc.id) from users as uc where uc.company_id = c.id and uc.deleted_at is null) as users_count
                from companies as c
                where c.deleted_at is null
                order by c.name ' . $order . '
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
            ->where('roles.name', '=', RoleConstant::COMPANY_ADMIN)
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

            $company = Company::withTrashed()
                ->where('name', '=', $credentials['companyName'])
                ->first();

            if ( $company ) {
                $company->restore();
            } else {
                $company = Company::on()->create([
                    'name' => $credentials['companyName'],
                ]);
            }

            $user = User::withTrashed()
                ->where('email', '=', $credentials['email'])
                ->first();

            if ( ! is_null( $user ) ) {
                $user->restore();
                $user->update([
                    'company_id'        => $company->id,
                    'department_id'     => null,
                    'first_name'        => $credentials['firstName'],
                    'last_name'         => $credentials['lastName'],
                    'about'             => null,
                    'email'             => $credentials['email'],
                    'email_verified_at' => null,
                    'password'          => bcrypt($credentials['password']),
                    'remember_token'    => null,
                    'avatar'            => null,
                    'rank'              => null,
                    'profile_image'     => null,
                ]);
            } else {
                $user = User::on()->create([
                    'company_id'    => $company->id,
                    'first_name'    => $credentials['firstName'],
                    'last_name'     => $credentials['lastName'],
                    'email'         => $credentials['email'],
                    'password'      => bcrypt($credentials['password']),
                ]);

                $user->assignRole(RoleConstant::COMPANY_ADMIN);
            }

            $this->sendInvitationToNewCompany($credentials['email']);
        });
    }

    /**
     * Changes to company information and administrator credentials of that company.
     *
     * @param Company $company
     * @param array $credentials
     * @throws \Exception
     */
    public function update(Company $company, array $credentials): void
    {
        // ['companyName', 'firstName','lastName', 'email', 'password']
        $userAdminCompany = User::on()
            ->select(DB::raw('
                users.id as id,
                users.first_name as first_name,
                users.last_name as last_name,
                users.email as email,
                users.password as password
            '))
            ->leftJoin('model_has_roles', function ($join) {
                $join->on('users.id', '=', 'model_has_roles.model_id');
            })
            ->leftJoin('roles', function ($join) {
                $join->on('roles.id', '=', 'model_has_roles.role_id');
            })
            ->where('users.company_id', '=', $company->id)
            ->where('roles.name', '=', RoleConstant::COMPANY_ADMIN)
            ->get();

        if ( $userAdminCompany->count() != 1 ) {
            return;
        }

        $isEmailBusy = 0;
        if ( ( ! is_null($credentials['email']) ) && ($userAdminCompany[0]->email != $credentials['email']) ) {
            $isEmailBusy = User::on()
                ->where('email', '=', $credentials['email'])
                ->where('id', '!=', $userAdminCompany[0]->id)
                ->count();
        }

        if ( $isEmailBusy == 1 ) {
            throw new \Exception('This email is reserved by another user', Response::HTTP_CONFLICT);
        }

        DB::transaction( function () use ( $company, $userAdminCompany, $credentials ) {
            $company->name = $credentials['companyName'];
            $company->save();

            $userAdminCompany[0]->first_name = $credentials['firstName'];
            $userAdminCompany[0]->last_name = $credentials['lastName'];
            if ( isset($credentials['email']) && ! is_null($credentials['email']) ) {
                $userAdminCompany[0]->email = $credentials['email'];
            }
            if ( isset($credentials['password']) && ( ! is_null( $credentials['password'] ) ) ) {
                $userAdminCompany[0]->password = bcrypt($credentials['password']);
            }
            $userAdminCompany[0]->save();
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
            $usersIDs = User::on()
                ->where('company_id', '=', $company->id)
                ->get('id')
                ->map(function ($item) {
                    return $item->id;
                })
                ->toArray();

            Invite::on()->whereIn('user_id', $usersIDs)->delete();

            User::on()->where('company_id', '=', $company->id)->delete();
            Company::on()->where('id', '=', $company->id)->delete();
        });
    }
}
