<?php

namespace App\Repositories;

use App\Constants\RoleConstant;
use App\Helpers\Helper;
use App\Helpers\HelperRankServiceClient;
use App\Models\Company;
use App\Models\Department;
use App\Models\Shoogle;
use App\Models\UserHasShoogle;
use App\Models\WellbeingScores;
use App\Scopes\ShoogleScope;
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
     * Low well-being scores.
     */
    const SCORES_LOW = 2;

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
     * Checking a user for existence.
     *
     * @param int $userId
     * @throws Exception
     */
    public function existsUser(int $userId): void
    {
        $user = User::on()->find($userId);

        if ( is_null( $user ) ) {
            throw new Exception('User not found!', Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * If the user does not exist then throw an exception.
     *
     * @param int $id
     * @throws Exception
     */
    public function existsShoogle(int $id): void
    {
        $shoogle = Shoogle::on()->find($id);

        if ( is_null( $shoogle ) ) {
            throw new Exception('Shoogle not found!', Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Is there a shoogle among the blocked.
     *
     * @param int $shoogleId
     * @throws Exception
     */
    public function existsShoogleAmongBlocked(int $shoogleId): void
    {
        $shoogle = Shoogle::on()
            ->withoutGlobalScope(ShoogleScope::class)
            ->find($shoogleId);

        if ( is_null( $shoogle ) ) {
            throw new Exception('Shoogle not found!', Response::HTTP_NOT_FOUND);
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
            throw new Exception('Company not found!', Response::HTTP_NOT_FOUND);
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
            throw new Exception('Department not found!', Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Calculate the average well-being for one user.
     *
     * @param int $userId
     * @param string|null $dateFrom
     * @param string|null $dateTo
     * @return object
     */
    public function getAverageUser(int $userId, ?string $dateFrom, ?string $dateTo): ?object
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

            ->when( ! is_null($dateFrom), function($query) use ($dateFrom) {
                return $query->where('created_at', '>=', $dateFrom . ' 00:00:00');
            })

            ->when( ! is_null($dateTo), function($query) use ($dateTo) {
                return $query->where('created_at', '<=', $dateTo . ' 23:59:59');
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
     * @throws \GetStream\StreamChat\StreamException
     */
    public function storeScores(int $userId, array $scores)
    {
        $this->model->on()->create([
            'user_id'       => $userId,
            'social'        => $scores['social'],
            'physical'      => $scores['physical'],
            'mental'        => $scores['mental'],
            'economical'    => $scores['financial'],
            'spiritual'     => $scores['spiritual'],
            'emotional'     => $scores['financial'],
            'intellectual'  => $scores['intellectual'],
        ]);

        HelperRankServiceClient::assignRank($userId);
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

    /**
     * Returns the wellbeing point level.
     *
     * @param int|null $userId
     * @return bool
     */
    public function getScoresLow(?int $userId): bool
    {
        $scoresLow = true;

        if ( is_null($userId) ) {
            return false;
        }

        $user = User::on()
            ->where('id', '=', $userId)
            ->first();

        if ( is_null($user) ) {
            return false;
        }

        $averageUser = (array)$this->getAverageUser($userId, null, null);

        foreach ($averageUser as $average) {

            if ( is_null($average) || $average <= self::SCORES_LOW ) {
                $scoresLow = false;
            }

        }

        return $scoresLow;
    }
}
