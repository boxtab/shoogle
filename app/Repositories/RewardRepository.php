<?php

namespace App\Repositories;

use App\Constants\RoleConstant;
use App\Helpers\Helper;
use App\Models\Reward;
use App\Models\UserHasReward;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use \Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\URL;

/**
 * Class RewardRepository
 * @package App\Repositories
 */
class RewardRepository extends Repositories
{
    /**
     * @var Reward
     */
    protected $model;

    /**
     * RewardRepository constructor.
     * @param Reward $model
     */
    public function __construct(Reward $model)
    {
        parent::__construct($model);
    }

    /**
     * Assign a reward to a user.
     *
     * @param int $userId
     * @param int $rewardId
     */
    public function assign(int $userId, int $rewardId): void
    {
        UserHasReward::updateOrCreate(
            ['user_id' => $userId, 'reward_id' => $rewardId],
            ['given_by_user_id' => Auth::id()]
        );
    }

    /**
     * List of rewards.
     *
     * @return mixed
     */
    public function getList()
    {
        return $this->model
            ->get()
            ->map(function($item) {
                $item['icon'] = URL::to('/') . '/' . $item['icon'];
                return $item;
            });
    }
}
