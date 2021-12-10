<?php

namespace App\Helpers;

use App\Models\ConfigCronLastFlagProcessedAt;
use App\Services\StreamService;
use DateTime;
use DateTimeInterface;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * Class HelperConfigCron
 * @package App\Helpers
 */
class HelperConfigCron
{
    /**
     * Get last flag.
     *
     * @return string|null
     */
    public static function getLastFlagProcessed(): ?string
    {
        $configCronLastFlagProcessedAt = ConfigCronLastFlagProcessedAt::on()->first();

        if ( is_null($configCronLastFlagProcessedAt) ) {
            return null;
        }

        return $configCronLastFlagProcessedAt->value;
    }

    /**
     * Set last flag.
     *
     * @param string|null $value
     */
    public static function setLastFlagProcessed(?string $value)
    {
        DB::transaction(function () use($value) {
            ConfigCronLastFlagProcessedAt::on()->delete();
            ConfigCronLastFlagProcessedAt::on()->insert(['value' => $value]);
        });
    }

    /**
     * return:
     * 0 if dates same,
     * -1 if first latest,
     * 1 if second latest
     *
     * @param string|null $date1
     * @param string|null $date2
     * @return int
     * @throws Exception
     */
    public static function isDateWithMaxAccuracyLate(string $date1, string $date2): int
    {
        $date1D = new DateTime($date1);
        $date2D = new DateTime($date2);
        if($date1D < $date2D) return 1;
        if($date1D > $date2D) return -1;
        if($date1 < $date2) return 1;
        if($date1 > $date2) return -1;
        return 0;
    }

    /**
     * @return array
     * @throws Exception
     */
    public static function getMessagesWithFlag(): array
    {
        $service = new StreamService(-1);
        $latestDate = (new DateTime('1970'))->format(DateTimeInterface::RFC3339_EXTENDED);
        $lastTime = self::getLastFlagProcessed() ?? $latestDate;
        $resList = [];
        $page = 1;

        while(true) {
            $list = $service->getFlagList($page);
            if (empty($list)) break;
            $page++;
            foreach ($list as $flag) {
                $dateInString = $flag['created_at'];
                if(self::isDateWithMaxAccuracyLate($latestDate, $dateInString) == 1) $latestDate = $flag['created_at'];
                if(self::isDateWithMaxAccuracyLate($lastTime, $dateInString) != 1) break 2;
                array_push($resList, $flag);
            }
        }

        self::setLastFlagProcessed($latestDate);

        return $resList;
    }
}
