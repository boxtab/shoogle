<?php

namespace App\Repositories;

use App\Constants\RoleConstant;
use App\Helpers\Helper;
use App\Models\Shoogle;
use App\Models\UserHasShoogle;
use App\Models\WellbeingScores;
use App\User;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
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
    public function existsUser(int $id): void
    {
        $user = User::find($id);

        if ( is_null( $user ) ) {
            throw new Exception('User not found for this ID', Response::HTTP_NOT_FOUND);
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
        $user = Shoogle::find($id);

        if ( is_null( $user ) ) {
            throw new Exception('Shoogle not found for this ID', Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Calculate the average well-being for one user.
     *
     * @param int $userId
     * @param string $from
     * @param string $to
     * @return object
     */
    public function getAverageUser(int $userId, string $from, string $to): object
    {
//        return $this->model
//            ->select(DB::raw('
//                    AVG(social) as social,
//                    AVG(physical) as physical,
//                    AVG(mental) as mental,
//                    AVG(economical) as economical,
//                    AVG(spiritual) as spiritual,
//                    AVG(emotional) as emotional
//                '))
//            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
//            ->where('user_id', $userId)
//            ->first();
//
        $selection = $this->model
            ->select(DB::raw('
                    social,
                    physical,
                    mental,
                    economical,
                    spiritual,
                    emotional
                '))
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->where('user_id', $userId)
            ->get();

        $average = [
            'social' => collect($selection)->average('social'),
            'physical' => collect($selection)->average('physical'),
            'mental' => collect($selection)->average('mental'),
            'economical' => collect($selection)->average('economical'),
            'spiritual' => collect($selection)->average('spiritual'),
            'emotional' => collect($selection)->average('emotional'),
        ];

        return (object)$average;
    }

    /**
     * Getting average points of well-being by shoogle.
     *
     * @param int $shoogleId
     * @param string $from
     * @param string $to
     * @return object
     */
    public function getAverageShoogle(int $shoogleId, string $from, string $to): object
    {
        $arrayUserId = UserHasShoogle::where('shoogle_id', $shoogleId)
            ->select('user_id')
            ->get()
            ->map(function ($item) {
                return $item->user_id;
            })
            ->toArray();

        $countUser = count($arrayUserId);

        $average = [
            'social' => 0,
            'physical' => 0,
            'mental' => 0,
            'economical' => 0,
            'spiritual' => 0,
            'emotional' => 0,
        ];

        foreach ($arrayUserId as $userId) {
            $averageUser = $this->getAverageUser($userId, $from, $to);
            $average['social'] += $averageUser->social;
            $average['physical'] += $averageUser->physical;
            $average['mental'] += $averageUser->mental;
            $average['economical'] += $averageUser->economical;
            $average['spiritual'] += $averageUser->spiritual;
            $average['emotional'] += $averageUser->emotional;
        }

        if ( $countUser > 0 ) {
            $average['social'] = $average['social'] / $countUser;
            $average['physical'] = $average['physical'] / $countUser;
            $average['mental'] = $average['mental'] / $countUser;
            $average['economical'] = $average['economical'] / $countUser;
            $average['spiritual'] = $average['spiritual'] / $countUser;
            $average['emotional'] = $average['emotional'] / $countUser;
        }

        return (object)$average;
    }
}
