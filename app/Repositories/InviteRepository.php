<?php

namespace App\Repositories;

use App\Models\Invite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

/**
 * Class InviteRepository
 * @package App\Repositories
 */
class InviteRepository extends Repositories
{
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
        $companyId = getCompanyIdFromJWT();

        return $this->model
            ->select(DB::raw('
                invites.id as id,
                invites.email as email,
                invites.is_used as is_used,
                invites.companies_id as companies_id'))
            ->when( ! is_null($companyId), function($query) use ($companyId) {
                return $query->where('invites.companies_id', $companyId);
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
        $companyId = getCompanyIdFromJWT();

        if ( ! is_null($companyId) ) {
            $this->model->create([
                'email' => $email,
                'is_used' => 0,
                'created_by' => Auth::user()->id,
                'companies_id' => $companyId,
            ]);
        }
    }
}
