<?php

namespace App\Repositories;

use App\Models\UserHasShoogleLog;

/**
 * Class UserHasShoogleLogRepository
 * @package App\Repositories
 */
class UserHasShoogleLogRepository extends Repositories
{
    /**
     * @var UserHasShoogleLog
     */
    protected $model;

    /**
     * UserHasShoogleLogRepository constructor.
     * @param UserHasShoogleLog $model
     */
    public function __construct(UserHasShoogleLog $model)
    {
        parent::__construct($model);
    }
}
