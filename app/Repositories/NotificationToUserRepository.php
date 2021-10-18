<?php

namespace App\Repositories;

use App\Models\NotificationToUser;
use Illuminate\Database\Eloquent\Model;

/**
 * Class NotificationToUserRepository
 * @package App\Repositories
 */
class NotificationToUserRepository extends Repositories
{
    /**
     * @var NotificationToUser
     */
    protected $model;

    /**
     * NotificationToUserRepository constructor.
     * @param NotificationToUser $model
     */
    public function __construct(NotificationToUser $model)
    {
        parent::__construct($model);
    }

}
