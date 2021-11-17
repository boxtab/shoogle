<?php

namespace App\Services;

use App\Helpers\HelperDateTime;
use App\Helpers\HelperNow;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
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

    private $lastNotification;

    private $eventsDateTime = [];

    /**
     * RruleService constructor.
     * @param string $dateStart
     * @param string $rruleString
     * @param string|null $lastNotification
     */
    public function __construct(string $dateStart, string $rruleString, ?string $lastNotification)
    {
        $this->dateStart = $dateStart;
        $this->rruleString = $rruleString;
        $this->lastNotification = $lastNotification;
    }

    /**
     * @throws \Recurr\Exception\InvalidWeekday
     * @throws \Exception
     */
    public function generateEventsDates(): void
    {
        $dateMidnight = (new \DateTime($this->dateStart))->format('Y-m-d') . ' 00:00:00';
        $datePlusYear = date('Y-m-d H:i:s', strtotime('+1 year', strtotime($this->dateStart)));

        $beginDate = new \DateTime($dateMidnight);
        $endDate = new \DateTime($datePlusYear);

        try {
            $rule = new Rule( $this->rruleString, new \DateTime($this->dateStart) );
        } catch (InvalidRRule $e) {
        }

        $transformer = new ArrayTransformer();
        $constraint = new BetweenConstraint($beginDate, $endDate);

        $eventsDatesObject = $transformer->transform($rule, $constraint);

        foreach ($eventsDatesObject as $eventDate) {
            $this->eventsDateTime[] = $eventDate->getStart()->format('Y-m-d H:i:s');
        }
    }

    /**
     * @return array
     */
    public function getEventsDateTime(): array
    {
        return $this->eventsDateTime;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getEventsDate(): array
    {
        $eventsDate = [];
        foreach ($this->eventsDateTime as $eventDateTime) {
            $eventsDate[] = (new \DateTime($eventDateTime))->format('Y-m-d');
        }
        return $eventsDate;
    }

    /**
     * @return array
     */
    public function getEventsTimestamp(): array
    {
        $eventsTimestamp = [];
        foreach ($this->eventsDateTime as $eventDateTime) {
            $eventsTimestamp[] = strtotime($eventDateTime);
        }
        return $eventsTimestamp;
    }

    /**
     * @param string|null $date
     * @return bool
     * @throws \Exception
     */
    public function eventHasCome(): bool
    {
        $lastNotificationDate = (new \DateTime($this->lastNotification))->format('Y-m-d');
        $currentDate = Carbon::now()->toDateString();
//        $currentDate = HelperNow::getDate();

        if ( ! is_null($this->lastNotification) && $lastNotificationDate === $currentDate ) {
            return false;
        }

        $key = array_search($currentDate, $this->getEventsDate());
        if ( $key === false ) {
            return false;
        }

        $now = Carbon::now()->timestamp;
//        $now = HelperNow::getTimestamp();

        $eventDate = $this->getEventsTimestamp()[$key];

        if ( $now >= $eventDate ) {
            return true;
        }

        return false;
    }
}
