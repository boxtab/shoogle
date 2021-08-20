<?php

namespace App\Repositories;

use App\Helpers\Helper;
use App\Models\Shoogle;
use App\Models\UserHasShoogle;
use App\User;
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
    /**
     * @var Shoogle
     */
    protected $model;

    /**
     * ShooglerRepository constructor.
     * @param UserHasShoogle $model
     */
    public function __construct(UserHasShoogle $model)
    {
        parent::__construct($model);
    }


    public function getList(int $shoogleId)
    {
        return 123;
    }
}

