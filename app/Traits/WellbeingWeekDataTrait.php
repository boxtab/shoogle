<?php

namespace App\Traits;

use App\Models\WellbeingScores;
use Illuminate\Support\Facades\Log;

/**
 * Trait WellbeingWeekDataTrait
 * @package App\Traits
 */
trait WellbeingWeekDataTrait
{
    /**
     * @var array $averageByWeek
     */
    private $averageByWeek = [
        'social'        => [],
        'physical'      => [],
        'mental'        => [],
        'economical'    => [],
        'spiritual'     => [],
        'emotional'     => [],
        'intellectual'  => [],
    ];

    /**
     * Return average data by week.
     *
     * @param array $usersIDs
     * @param string $dateFrom
     * @param string $dateTo
     * @return array
     * @throws \Exception
     */
    private function getWellbeingData(array $usersIDs, string $dateFrom, string $dateTo): array
    {
        $beginEndInRange = $this->getBeginEndInRange($dateFrom, $dateTo);

        foreach ($beginEndInRange as $range) {
            $usersIdsOfWeek = $this->getUsersIdsOfWeek($usersIDs, $range[0], $range[1]);
            if ( count($usersIdsOfWeek) > 0 ) {
                null;
            } else {
                $this->fillZero();
            }
        }

        return $this->averageByWeek;
    }

    /**
     * Range divided into weeks.
     *
     * @param string $dateFromString
     * @param string $dateToString
     * @return array
     * @throws \Exception
     */
    private function getBeginEndInRange(string $dateFromString, string $dateToString): array
    {
        $dateFrom = new \DateTime($dateFromString);
        $dateTo = new \DateTime($dateToString);
        $dates = [];

        if ($dateFrom > $dateTo) {
            return $dates;
        }

        $i = 0;
        while ($dateFrom <= $dateTo) {
            $dates[$i] = [$dateFrom->format('Y-m-d') . ' 00:00:00'];
            $dateEnd = new \DateTime($dateFrom->format('Y-m-d') . ' 00:00:00');
            $dateEnd->modify('+6 day');
            $dates[$i][] = ($dateTo < $dateEnd) ? $dateTo->format('Y-m-d') . ' 23:59:59' : $dateEnd->format('Y-m-d') . ' 23:59:59';
            $dateFrom->modify('+1 week');
            $i++;
        }

        return $dates;
    }

    /**
     * Returns user ids for a week.
     *
     * @param array $usersIds
     * @param string $dateFrom
     * @param string $dateTo
     * @return array
     */
    private function getUsersIdsOfWeek(array $usersIds, string $dateFrom, string $dateTo): array
    {
        return WellbeingScores::on()
            ->whereIn('user_id', $usersIds)
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->groupBy('user_id')
            ->get()
            ->map(function ($query) {
                return $query->user_id;
            })
            ->toArray();
    }

    /**
     * Fill indicators with zeros.
     */
    private function fillZero()
    {
        foreach ($this->averageByWeek as $key => $item) {
            $this->averageByWeek[$key][] = 0;
        }
    }


}
