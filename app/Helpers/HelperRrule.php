<?php

namespace App\Helpers;

use App\Services\RruleService;
use Recurr\Exception\InvalidWeekday;
use Recurr\Rule;
use Recurr\Transformer\ArrayTransformer;
use Recurr\Transformer\Constraint\BetweenConstraint;

/**
 * Class HelperRrule
 * @package App\Helpers
 */
class HelperRrule
{
    /**
     * @param string $dateStart
     * @param string $rruleString
     * @param string|null $lastNotification
     * @return bool
     * @throws InvalidWeekday
     */
    public static function eventHasCome(string $dateStart, string $rruleString, ?string $lastNotification): bool
    {
        $rruleService = new RruleService($dateStart, $rruleString, $lastNotification);
        $rruleService->generateEventsDates();

        return $rruleService->eventHasCome();
    }
}
