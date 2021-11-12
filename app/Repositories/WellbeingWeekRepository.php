<?php

namespace App\Repositories;

use App\Models\WellbeingScores;
use App\Traits\WellbeingWeekAverageTrait;
use App\Traits\WellbeingWeekIntervalTrait;
use Illuminate\Support\Facades\Log;

/**
 * Class WellbeingWeekRepository
 * @package App\Repositories
 */
class WellbeingWeekRepository extends Repositories
{
    use WellbeingWeekAverageTrait;
    use WellbeingWeekIntervalTrait;

    /**
     * @var array Wellbeing points by week.
     */
    private $wellbeing = [
        'wellbeing' => [
            'social'        => null,
            'physical'      => null,
            'mental'        => null,
            'economical'    => null,
            'spiritual'     => null,
            'emotional'     => null,
            'intellectual'  => null,
        ],
        'wellbeingData' => [
            'social'        => null,
            'physical'      => null,
            'mental'        => null,
            'economical'    => null,
            'spiritual'     => null,
            'emotional'     => null,
            'intellectual'  => null,
            'label'         => null,
        ],
    ];

    /**
     * @var WellbeingScores
     */
    protected $model;

    /**
     * WellbeingWeekRepository constructor.
     * @param WellbeingScores $model
     */
    public function __construct(WellbeingScores $model)
    {
        parent::__construct($model);
    }

    /**
     * @param array $usersIDs
     * @param string|null $dateFrom
     * @param string|null $dateTo
     * @return array
     */
    public function getDataByWeek(array $usersIDs, ?string $dateFrom, ?string $dateTo): array
    {
        if ( ! empty($usersIDs) ) {
            $wellbeing = $this->getWeekAverage($usersIDs, $dateFrom, $dateTo);
            $this->fillTheWellbeing($wellbeing);

            $beginDate = $this->getBeginInterval($usersIDs, $dateFrom);
            $endDate = null;

            Log::info('beginDate');
            Log::info($beginDate);
        }
        return $this->wellbeing;
    }

    /**
     * Complete Average Wellbeing Points.
     *
     * @param array $wellbeing
     */
    private function fillTheWellbeing(array $wellbeing)
    {
        if ( is_null($wellbeing) ) {
            return;
        }

        foreach ($this->wellbeing['wellbeing'] as $key => $item) {
            $this->wellbeing['wellbeing'][$key] = $wellbeing[$key];
        }
    }
}
