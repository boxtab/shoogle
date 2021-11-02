<?php

namespace App\Repositories;

use App\Models\WellbeingScores;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CommunityLevelRepository
 * @package App\Repositories
 */
class CommunityLevelRepository extends Repositories
{
    /**
     * @var WellbeingScores
     */
    protected $model;

    /**
     * CommunityLevelRepository constructor.
     * @param WellbeingScores $model
     */
    public function __construct(WellbeingScores $model)
    {
        parent::__construct($model);
    }
}
