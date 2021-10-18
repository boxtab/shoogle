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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class ShooglerRepository
 * @package App\Repositories
 */
class ShooglerRepository extends Repositories
{
    use ShooglerTrait;

    const OUTDATED = 3;

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
     * @param string|null $order
     * @param int|null $page
     * @param int|null $pageSize
     * @return array|null
     * @throws \ReflectionException
     */
    public function getShooglerList(int $shoogleId, ?string $search, ?string $filter, ?string $order, ?int $page, ?int $pageSize)
    {
        $this->shoogleID = $shoogleId;
        $shooglersIDs = $this->getShooglersIDsByShoogleID($shoogleId, Auth::id());
        $orderProcessed = ( $order === 'oldest' ? 'asc' : 'desc' );

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
                return $query->whereRaw("CONCAT(u.first_name, ' ', u.last_name) LIKE '%$search%'");
            })
            ->orderBy('u.created_at', $orderProcessed)
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

