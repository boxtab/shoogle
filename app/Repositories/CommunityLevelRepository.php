<?php

namespace App\Repositories;

use App\Helpers\HelperCompany;
use App\Models\WellbeingScores;
use App\Traits\CommunityLevelDataEmpty;
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
    use CommunityLevelDataEmpty;

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
     * @param array|null $usersIDs
     * @param string|null $dateFrom
     * @param string|null $dateTo
     * @return array
     */
    public function getWellbeingCategory(?array $usersIDs, ?string $dateFrom, ?string $dateTo)
    {
        if ( ! is_null($usersIDs) ) {

            $periodBegin = $this->getPeriodBegin($usersIDs, $dateFrom, $dateTo);
            $periodEnd = $this->getPeriodEnd($usersIDs, $dateFrom, $dateTo);

            if ( is_null($periodBegin) || is_null($periodEnd) || empty($usersIDs) ) {
                return $this->wellbeingCategory;
            }

            $differenceValue = $this->getDifferenceValue($usersIDs, $periodBegin, $periodEnd);
            $isGrew = $this->getIsGrew($usersIDs, $periodBegin, $periodEnd);
            $value = $this->getValue($usersIDs, $periodBegin, $periodEnd);

            $this->fillTheField($differenceValue, 'differenceValue');
            $this->fillTheField($isGrew, 'isGrew');
            $this->fillTheField($value, 'value');
        }

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
