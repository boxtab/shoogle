<?php

namespace App\Repositories;

use App\Http\Requests\InviteCSVRequest;
use App\Mail\API\V1\InviteMail;
use App\Models\Invite;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

/**
 * Class InviteRepository
 * @package App\Repositories
 */
class InviteRepository extends Repositories
{
    const COUNT_FIELD = 1;

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
                invites.companies_id as companies_id'))
            ->when( ! $this->noCompany(), function($query) {
                return $query->where('invites.companies_id', $this->companyId);
            })
            ->get();
    }

    /**
     * Creating a single invite.
     *
     * @param string $email
     */
    public function create(string $email): void
    {
        if ( $this->noCompany() ) {
            return;
        }

        $invite = $this->model->create([
            'email' => $email,
            'is_used' => 0,
            'created_by' => Auth::user()->id,
            'companies_id' => $this->companyId,
        ]);

        if ( ! is_null($invite) ) {
            $this->sendInvitationsToEmail([$email]);
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

            $invite = Invite::on()->where('email', $inviteRow[0])->first();
            if ($invite !== null) {
                $invite->update([
                    'is_used' => 0,
                    'created_by' => Auth::id(),
                    'companies_id' => $this->companyId,
                ]);
            } else {
                $invite = new Invite();
                $invite->email = $inviteRow[0];
                $invite->is_used = 0;
                $invite->created_by = Auth::id();
                $invite->companies_id = $this->companyId;
                $invite->save();
            }

            $listEmail[] = $inviteRow[0];
        }
        $this->sendInvitationsToEmail($listEmail);
    }

    /**
     * Send invitations to email.
     *
     * @param array $listEmail
     */
    private function sendInvitationsToEmail(array $listEmail): void
    {
        if ( empty( $listEmail ) ) {
            return;
        }

        if ( ! $this->isCredentialsEmail() ) {
            return;
        }

        $inviteMail = new InviteMail();
        foreach ($listEmail as $email) {
            $inviteMail->to($email);
            Mail::send($inviteMail);
        }
    }

    /**
     * Is there any credentials for email.
     *
     * @return bool
     */
    private function isCredentialsEmail()
    {
        $credentials = true;

        if ( empty( env('MAIL_MAILER') ) ) {
            $credentials = false;
        }

        if ( empty( env('MAIL_HOST') ) ) {
            $credentials = false;
        }

        if ( empty( env('MAIL_PORT') ) ) {
            $credentials = false;
        }

        if ( empty( env('MAIL_USERNAME') ) ) {
            $credentials = false;
        }

        if ( empty( env('MAIL_PASSWORD') ) ) {
            $credentials = false;
        }

        if ( empty( env('MAIL_ENCRYPTION') ) ) {
            $credentials = false;
        }

        if ( empty( env('MAIL_FROM_ADDRESS') ) ) {
            $credentials = false;
        }

        if ( empty( env('MAIL_FROM_NAME') ) ) {
            $credentials = false;
        }

        return $credentials;
    }
}
