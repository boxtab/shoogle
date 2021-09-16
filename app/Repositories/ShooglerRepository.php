<?php

namespace App\Repositories;

use App\Helpers\Helper;
use App\Models\Shoogle;
use App\Models\UserHasShoogle;
use App\Traits\ShooglerTrait;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class ShooglerRepository
 * @package App\Repositories
 */
class ShooglerRepository extends Repositories
{
    use ShooglerTrait;

    const OUTDATED = 1;

    /**
     * @var Shoogle
     */
    protected $model;

    /**
     * @var int
     */
    private $shoogleID;

    /**
     * ShooglerRepository constructor.
     * @param UserHasShoogle $model
     */
    public function __construct(UserHasShoogle $model)
    {
        parent::__construct($model);
    }

    /**
     * List of shooglers.
     *
     * @param int $shoogleId
     * @param string|null $search
     * @param string|null $filter
     * @param int|null $page
     * @param int|null $pageSize
     * @return array|null
     * @throws \ReflectionException
     */
    public function getShooglerList(int $shoogleId, string $search = null, string $filter = null, int $page = null, int $pageSize = null)
    {
        $this->shoogleID = $shoogleId;
        $shooglersIDs = $this->getShooglersIDsByShoogleID($shoogleId);

        $shooglers = DB::table('users as u')
            ->select(DB::raw('
                u.id as id,
                u.profile_image as profile_image,
                u.first_name as firstName,
                u.last_name as lastName,
                u.about as about,
                null as baddies,
                null as solo,
                null as joinedAt
            '))
            ->whereIn('u.id', $shooglersIDs)
            ->when( ! is_null($search), function($query) use ($search) {
                return $query->where(function ($query) use ($search) {
                    return $query->where('u.first_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('u.email', 'LIKE', '%' . $search . '%');
                });
            })
            ->offset($page * $pageSize - $pageSize)
            ->limit($pageSize)
            ->get()
            ->toArray();

        $shooglers = $this->setBaddies($shooglers);
        $shooglers = $this->setSolo($shooglers);
        $shooglers = $this->setJoinedAt($shooglers);
        $shooglers = $this->filter($shooglers, $filter);

        return $shooglers;
    }
}

