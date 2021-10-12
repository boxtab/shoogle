<?php

namespace App\Repositories;

use App\Constants\EnvConstant;
use App\Http\Requests\InviteCSVRequest;
use App\Mail\API\V1\InviteMail;
use App\Models\Department;
use App\Models\Invite;
use App\Support\ApiResponse\ApiResponse;
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
        if ( $this->noCompany() ) {
            throw new Exception('The authenticated user does not have a company ID!', Response::HTTP_NOT_FOUND);
        }

        $invite = $this->model->on()->create([
            'email' => $email,
            'is_used' => 0,
            'created_by' => Auth::user()->id,
            'companies_id' => $this->companyId,
            'department_id' => $departmentId,
        ]);

        if ( ! is_null( $invite ) ) {
            $this->sendInvitationsToEmail([$email]);
        } else {
            throw new Exception('An invitation record was not created in the table!', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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

            if (Department::on()->where('id', $inviteRow[1])->count() != 1) {
                continue;
            }

            $invite = Invite::on()->where('email', $inviteRow[0])->first();
            if ($invite !== null) {
                $invite->update([
                    'is_used' => 0,
                    'created_by' => Auth::id(),
                    'companies_id' => $this->companyId,
                    'department_id' => $inviteRow[1],
                ]);
            } else {
                $invite = new Invite();
                $invite->email = $inviteRow[0];
                $invite->is_used = 0;
                $invite->created_by = Auth::id();
                $invite->companies_id = $this->companyId;
                $invite->department_id = $inviteRow[1];
                $invite->save();
            }

            $listEmail[] = $inviteRow[0];
        }
//        $this->sendInvitationsToEmail($listEmail);
    }

    /**
     * Send invitations to email.
     * @param array $listEmail
     * @throws Exception
     */
    private function sendInvitationsToEmail(array $listEmail): void
    {
        if ( empty( $listEmail ) ) {
            throw new Exception('Email list to send is empty!', Response::HTTP_INTERNAL_SERVER_ERROR);
        }


        $credentialsEmail = $this->isCredentialsEmail();
        if ( $credentialsEmail !== false ) {
            throw new Exception("$credentialsEmail variable not found in environment file!", Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if ( empty( config('mail.invite.email_from') ) ) {
            throw new Exception("The environment file does not specify from whom to send mail!", Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if ( empty( config('mail.invite.subject') ) ) {
            throw new Exception("The email subject is not specified in the environment file!", Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        try {
            $inviteMail = new InviteMail();
            $inviteMail->to($listEmail[0]);
            Mail::send($inviteMail);
        } catch (Exception $e) {
            $error = $e->getMessage();
            throw new Exception("EMAIL NOT SENT! $error", Response::HTTP_BAD_GATEWAY);
        }

//        $inviteMail = new InviteMail();
//        foreach ($listEmail as $email) {
//            $inviteMail->to($email);
//            Mail::send($inviteMail);
//        }
    }

    /**
     * Is there any credentials for email.
     *
     * @return bool
     */
    private function isCredentialsEmail()
    {
        $credentials = false;
        $countVarEnv = 0;

        while ($countVarEnv < count(EnvConstant::$emailInvite)) {

            if ( empty( env( EnvConstant::$emailInvite[$countVarEnv] ) ) ) {
                $credentials = EnvConstant::$emailInvite[$countVarEnv];
                break;
            }
            $countVarEnv++;

        }
        return $credentials;
    }
}
