<?php

namespace App\Services;

use App\Helpers\HelperDateTime;
use Carbon\Carbon;
use Recurr\Exception\InvalidRRule;
use Recurr\Rule;
use Recurr\Transformer\ArrayTransformer;
use Recurr\Transformer\Constraint\BetweenConstraint;

/**
 * Class RruleService
 * @package App\Services
 */
class RruleService
{
    private $dateStart = '';

    private $rruleString = '';

    private $eventDates = [];

    /**
     * @return string
     */
    private function getTime(): string
    {
        return date('H:i:s', strtotime($this->dateStart));
    }

    /**
     * @param string $datetime
     * @return string
     */
    private function getYesterday(string $datetime): string
    {
        return date('Y-m-d H:i:s', strtotime('-1 day', strtotime($datetime)));
    }

    /**
     * @param string $datetime
     * @return string
     */
    private function getPlusOneYear(string $datetime): string
    {
        return date('Y-m-d H:i:s', strtotime('+1 year', strtotime($datetime)));
    }

    /**
     * RruleService constructor.
     * @param string $dateStart
     * @param string $rruleString
     */
    public function __construct(string $dateStart, string $rruleString)
    {
        $this->dateStart = $dateStart;
        $this->rruleString = $rruleString;
    }

    /**
     * @throws \Recurr\Exception\InvalidWeekday
     * @throws \Exception
     */
    public function generateEventsDates(): void
    {
        $beginDate = new \DateTime($this->getYesterday($this->dateStart));
        $endDate = new \DateTime($this->getPlusOneYear($this->dateStart));

        try {
            $rule = new Rule( $this->rruleString, new \DateTime('today midnight') );
        } catch (InvalidRRule $e) {
        }

        $transformer = new ArrayTransformer();
        $constraint = new BetweenConstraint($beginDate, $endDate);

        $eventsDatesObject = $transformer->transform($rule, $constraint);

        foreach ($eventsDatesObject as $eventDate) {
            $this->eventDates[] = $eventDate->getStart()->format('Y-m-d');
        }
    }

    /**
     * @return array
     */
    public function getEventDates(): array
    {
        return $this->eventDates;
    }

    /**
     * @param string $date
     * @return bool
     */
    public function eventHasCome(string $date): bool
    {
        $key = array_search($date, $this->eventDates);

        if ( $key === false ) {
            return false;
        }

        $now = Carbon::now()->timestamp;
        $eventDate = strtotime($date . ' ' . $this->getTime());

        if ( $now >= $eventDate ) {
            return true;
        }

        return false;
    }
}
