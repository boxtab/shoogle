<?php

namespace App\Repositories;

use App\Helpers\HelperCompany;
use App\Models\WellbeingScores;
use App\Traits\CommunityLevelDayTrait;
use App\Traits\CommunityLevelDifferenceValue;
use App\Traits\CommunityLevelIsGrewTrait;
use App\Traits\CommunityLevelUserTrait;
use App\Traits\CommunityLevelValueTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * Class CommunityLevelRepository
 * @package App\Repositories
 */
class CommunityLevelRepository extends Repositories
{
    use CommunityLevelDayTrait;
    use CommunityLevelUserTrait;

    use CommunityLevelDifferenceValue;
    use CommunityLevelIsGrewTrait;
    use CommunityLevelValueTrait;

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
        $periodBegin = $this->getNDaysAgo($period);
        $periodEnd = $this->getToday();
        $userIDs = $this->getUserIDs($companyId, $periodBegin, $periodEnd);

        $differenceValue = $this->getDifferenceValue($userIDs, $periodBegin, $periodEnd);
        $isGrew = $this->getIsGrew($userIDs, $periodBegin, $periodEnd);
        $value = $this->getValue($userIDs, $periodBegin, $periodEnd);

        $this->fillTheField($differenceValue, 'differenceValue');
        $this->fillTheField($isGrew, 'isGrew');
        $this->fillTheField($value, 'value');

        return $this->wellbeingCategory;
    }

    /**
     * Fill in data by key.
     *
     * @param array|null $data
     * @param string|null $field
     */
    private function fillTheField(?array $data, ?string $field): void
    {
        if ( is_null($data) || is_null($field) ) {
            return;
        }

        foreach ($this->wellbeingCategory as $key => $category) {
            if ( array_key_exists($key, $data) ) {
                if ( array_key_exists($field, $this->wellbeingCategory[$key]) ) {
                    $this->wellbeingCategory[$key][$field] = $data[$key];
                }
            }
        }
    }
}
