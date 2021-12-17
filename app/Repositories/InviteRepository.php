<?php

namespace App\Repositories;

use App\Constants\EnvConstant;
use App\Helpers\HelperCompany;
use App\Http\Requests\InviteCSVRequest;
use App\Mail\API\V1\InviteMail;
use App\Models\Department;
use App\Models\Invite;
use App\Support\ApiResponse\ApiResponse;
use App\Traits\InviteTrait;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Exception;

/**
 * Class InviteRepository
 * @package App\Repositories
 */
class InviteRepository extends Repositories
{
    use InviteTrait;

    const COUNT_FIELD = 2;

    /**
     * @var Invite
     */
    protected $model;

    /**
     * InviteRepository constructor.
     *
     * @param Invite $model
     */
    public function __construct(Invite $model)
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
                invites.id as id,
                invites.email as email,
                invites.is_used as is_used,
                invites.companies_id as companies_id,
                d.name as department,
                invites.created_at as created_at'))
            ->leftJoin('departments as d', 'invites.department_id', '=', 'd.id')
            ->when( ! $this->noCompany(), function($query) {
                return $query->where('invites.companies_id', $this->companyId);
            })
            ->get();
    }

    /**
     * Creating a single invite.
     *
     * @param string $email
     * @param int|null $departmentId
     * @throws Exception
     */
    public function create(string $email, ?int $departmentId): void
    {
        DB::transaction(function () use ($email, $departmentId) {
            if ( $this->noCompany() ) {
                throw new Exception('You are not part of more than one company!', Response::HTTP_NOT_FOUND);
            }

            $this->model->on()->create([
                'email' => $email,
                'is_used' => 0,
                'created_by' => Auth::user()->id,
                'companies_id' => $this->companyId,
                'department_id' => $departmentId,
            ]);

            $this->sendInvitationsToEmail($email);
        });
    }

    /**
     * Loading invites from a CSV file.
     *
     * @param string $pathFile
     */
    public function upload(string $pathFile): void
    {
        $fileCSV = array_map('str_getcsv', file($pathFile));
        $listEmail = [];
        $companyId = $this->companyId;
//        $companyId = HelperCompany::getCompanyId();

        foreach ($fileCSV as $inviteRow) {

            if (count($inviteRow) !== self::COUNT_FIELD) {
                continue;
            }

            if ( ! filter_var($inviteRow[0], FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            if (Invite::on()->where('email', $inviteRow[0])->count() > 0) {
                continue;
            }

            if (User::on()->where('email', $inviteRow[0])->count() > 0) {
                continue;
            }

            $department = Department::on()
                ->where('company_id', '=', $companyId)
                ->where('name', '=', $inviteRow[1])
                ->first();

            if ( ! is_null($department) ) {
                $departmentId = $department->id;
            } else {
                $newDepartment = new Department();
                $newDepartment->company_id = $companyId;
                $newDepartment->name = $inviteRow[1];
                $newDepartment->save();
                $departmentId = $newDepartment->id;
            }

//            if (
//                Department::on()
//                    ->where('company_id', '=', $companyId)
//                    ->where('id', '=', $inviteRow[1])
//                    ->count() != 1
//            ) {
//                continue;
//            }

            $invite = Invite::on()->where('email', $inviteRow[0])->first();
            if ($invite !== null) {
                $invite->update([
                    'is_used' => 0,
                    'created_by' => Auth::id(),
                    'companies_id' => $companyId,
                    'department_id' => $departmentId,
                ]);
            } else {
                $invite = new Invite();
                $invite->email = $inviteRow[0];
                $invite->is_used = 0;
                $invite->created_by = Auth::id();
                $invite->companies_id = $companyId;
                $invite->department_id = $departmentId;
                $invite->save();
            }

            $listEmail[] = $inviteRow[0];
        }
    }
}
