<?php

namespace App\Repositories;

use App\Constants\RoleConstant;
use App\Helpers\Helper;
use App\Models\Shoogle;
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
}
