<?php

namespace App\Repositories;

use App\Constants\RoleConstant;
use App\Helpers\Helper;
use App\Models\Company;
use App\Models\Department;
use App\Models\Shoogle;
use App\Models\UserHasShoogle;
use App\Models\WellbeingScores;
use App\User;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use phpDocumentor\Reflection\Types\Boolean;

/**
 * Class WellbeingScoresRepository
 * @package App\Repositories
 */
class WellbeingScoresRepository extends Repositories
{
    /**
     * @var WellbeingScores
     */
    protected $model;

    /**
     * WellbeingScoresRepository constructor.
     * @param WellbeingScores $model
     */
    public function __construct(WellbeingScores $model)
    {
        parent::__construct($model);
    }

    /**
     * If the user does not exist then throw an exception.
     *
     * @param int $id
     * @throws Exception
     */
    public function existsShoogle(int $id): void
    {
        $user = Shoogle::on()->find($id);

        if ( is_null( $user ) ) {
            throw new Exception('Shoogle not found for this ID', Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Company if a company exists by identifier.
     *
     * @param int|null $id
     * @throws Exception
     */
    public function existsCompany(?int $id): void
    {
        $company = Company::on()->find($id);

        if ( is_null( $company ) ) {
            throw new Exception('No company found by ID.', Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Department if a company exists by identifier.
     *
     * @param int|null $id
     * @throws Exception
     */
    public function existsDepartment(?int $id): void
    {
        $department = Department::on()->find($id);

        if ( is_null( $department ) ) {
            throw new Exception('No department found by ID.', Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Calculate the average well-being for one user.
     *
     * @param int $userId
     * @param string|null $from
     * @param string|null $to
     * @return object
     */
    public function getAverageUser(int $userId, string $from = null, string $to = null): ?object
    {
        $selection = $this->model->on()
            ->select(DB::raw('
                    social,
                    physical,
                    mental,
                    economical,
                    spiritual,
                    emotional,
                    intellectual
                '))
            ->when( (! is_null($from)) && (! is_null($to)), function($query) use ($from, $to) {
                return $query->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
            })
            ->where('user_id', $userId)
            ->get();

        $average = [
            'social'        => collect($selection)->average('social'),
            'physical'      => collect($selection)->average('physical'),
            'mental'        => collect($selection)->average('mental'),
            'economical'    => collect($selection)->average('economical'),
            'spiritual'     => collect($selection)->average('spiritual'),
            'emotional'     => collect($selection)->average('emotional'),
            'intellectual'  => collect($selection)->average('intellectual'),
        ];

        return (object)$average;
    }

    /**
     * Get the average from an array of users.
     *
     * @param array $arrayUserId
     * @param string|null $from
     * @param string|null $to
     * @return object|null
     */
    public function getAverageFromArrayUsers(array $arrayUserId, string $from = null, string $to=null): ?object
    {
        $countUser = count($arrayUserId);

        $average = [
            'social'        => 0,
            'physical'      => 0,
            'mental'        => 0,
            'economical'    => 0,
            'spiritual'     => 0,
            'emotional'     => 0,
            'intellectual'  => 0,
        ];

        foreach ($arrayUserId as $userId) {
            $averageUser = $this->getAverageUser($userId, $from, $to);
            $average['social'] += $averageUser->social;
            $average['physical'] += $averageUser->physical;
            $average['mental'] += $averageUser->mental;
            $average['economical'] += $averageUser->economical;
            $average['spiritual'] += $averageUser->spiritual;
            $average['emotional'] += $averageUser->emotional;
            $average['intellectual'] += $averageUser->intellectual;
        }

        if ( $countUser > 0 ) {
            $average['social'] = $average['social'] / $countUser;
            $average['physical'] = $average['physical'] / $countUser;
            $average['mental'] = $average['mental'] / $countUser;
            $average['economical'] = $average['economical'] / $countUser;
            $average['spiritual'] = $average['spiritual'] / $countUser;
            $average['emotional'] = $average['emotional'] / $countUser;
            $average['intellectual'] = $average['intellectual'] / $countUser;
        }

        return (object)$average;
    }

    /**
     * Getting average points of well-being by shoogle.
     *
     * @param int $shoogleId
     * @param string|null $from
     * @param string|null $to
     * @return object
     */
    public function getAverageShoogle(int $shoogleId, string $from = null, string $to=null): ?object
    {
        $arrayUserId = UserHasShoogle::on()
            ->where('shoogle_id', '=', $shoogleId)
            ->select('user_id')
            ->get()
            ->map(function ($item) {
                return $item->user_id;
            })
            ->toArray();

        return $this->getAverageFromArrayUsers($arrayUserId, $from, $to);
    }

    /**
     * Get the average for a company.
     *
     * @param string|null $from
     * @param string|null $to
     * @return object|null
     * @throws Exception
     */
    public function getAverageCompany(string $from = null, string $to=null): ?object
    {
        if ( $this->noCompany() ) {
            throw new \Exception('Company ID not found', Response::HTTP_NOT_FOUND);
        }

        $arrayUserId = User::on()
            ->where('company_id', $this->companyId)
            ->select('id')
            ->distinct()
            ->get()
            ->map(function ($item) {
                return $item->id;
            })
            ->toArray();

        return $this->getAverageFromArrayUsers($arrayUserId, $from, $to);
    }

    /**
     * Preservation of wellbeing-scores.
     *
     * @param int $userId
     * @param array $scores
     */
    public function storeScores(int $userId, array $scores)
    {
        $this->model->on()->create([
            'user_id'       => $userId,
            'social'        => $scores['social'],
            'physical'      => $scores['physical'],
            'mental'        => $scores['mental'],
            'economical'    => $scores['economical'],
            'spiritual'     => $scores['spiritual'],
            'emotional'     => $scores['emotional'],
            'intellectual'  => $scores['intellectual'],
        ]);
    }

    /**
     * Get Arithmetic Average by Company ID.
     *
     * @param int $companyId
     * @param string|null $from
     * @param string|null $to
     * @return object|null
     */
    public function getAverageCompanyId(int $companyId, ?string $from, ?string $to): ?object
    {
        $arrayUserId = User::on()
            ->where('company_id', $companyId)
            ->select('id')
            ->distinct()
            ->get()
            ->map(function ($item) {
                return $item->id;
            })
            ->toArray();

        return $this->getAverageFromArrayUsers($arrayUserId, $from, $to);
    }

    /**
     * Get Arithmetic Average by Department ID.
     *
     * @param int $departmentId
     * @param string|null $from
     * @param string|null $to
     * @return object|null
     */
    public function getDepartmentCompanyId(int $departmentId, ?string $from, ?string $to): ?object
    {
        $arrayUserId = User::on()
            ->where('department_id', '=', $departmentId)
            ->select('id')
            ->distinct()
            ->get()
            ->map(function ($item) {
                return $item->id;
            })
            ->toArray();

        return $this->getAverageFromArrayUsers($arrayUserId, $from, $to);
    }
}
