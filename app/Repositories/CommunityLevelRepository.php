<?php

namespace App\Repositories;

use App\Helpers\HelperCompany;
use App\Models\WellbeingScores;
use App\Traits\CommunityLevelDayTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * Class CommunityLevelRepository
 * @package App\Repositories
 */
class CommunityLevelRepository extends Repositories
{
    use CommunityLevelDayTrait;

    /**
     * @var array Company data by wellbeing category.
     */
    private $wellbeingCategory = [

        'social'        => ['differenceValue' => null, 'isGrew' => null, 'value' => null,],
        'physical'      => ['differenceValue' => null, 'isGrew' => null, 'value' => null,],
        'mental'        => ['differenceValue' => null, 'isGrew' => null, 'value' => null,],
        'economical'    => ['differenceValue' => null, 'isGrew' => null, 'value' => null,],
        'spiritual'     => ['differenceValue' => null, 'isGrew' => null, 'value' => null,],
        'emotional'     => ['differenceValue' => null, 'isGrew' => null, 'value' => null,],
        'intellectual'  => ['differenceValue' => null, 'isGrew' => null, 'value' => null,],

    ];

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

    /**
     * Company statistics by wellbeing category.
     *
     * @param $companyId
     * @param int $period
     * @return array
     */
    public function getWellbeingCategory($companyId, int $period)
    {
        $userIDs = HelperCompany::getArrayUserIds($companyId);

        $today = $this->getToday();

        Log::info($today);
        return $this->wellbeingCategory;
    }
}
