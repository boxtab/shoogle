<?php

namespace App\Repositories;

use App\Constants\RoleConstant;
use App\Helpers\Helper;
use App\Models\Shoogle;
use App\Models\UserHasShoogle;
use App\Models\WellbeingCategory;
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
 * Class WellbeingCategoryRepository
 * @package App\Repositories
 */
class WellbeingCategoryRepository extends Repositories
{
    /**
     * @var WellbeingCategory
     */
    protected $model;

    /**
     * WellbeingCategoryRepository constructor.
     * @param WellbeingCategory $model
     */
    public function __construct(WellbeingCategory $model)
    {
        parent::__construct($model);
    }

    public function getList()
    {
        return $this->model->get();
    }
}
