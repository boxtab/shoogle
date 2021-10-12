<?php

namespace App\Helpers;

use Recurr\Rule;
use Recurr\Transformer\ArrayTransformer;
use Recurr\Transformer\Constraint\BetweenConstraint;

class HelperRrule
{
    /**
     * @param string|null $rruleString
     * @return \DateTime|\DateTimeInterface
     * @throws \Recurr\Exception\InvalidRRule
     * @throws \Recurr\Exception\InvalidWeekday
     */
    public function getArrayTimestamp(string $dateStart, string $rruleString)
    {
        $dateStart = date('Y-m-d');
        $startDate = new \DateTime('today midnight');
        $endDate = new \DateTime('today +1 years 23:59:59');
        $rule = new Rule($rruleString, new \DateTime('today midnight'));
        $transformer = new ArrayTransformer();
        $constraint = new BetweenConstraint($startDate, $endDate);
        $eventsDates = $transformer->transform($rule, $constraint);

        foreach ($eventsDates as $eventDate) {
            $startDate = $eventDate->getStart();
        }

        return $startDate;
    }
}
