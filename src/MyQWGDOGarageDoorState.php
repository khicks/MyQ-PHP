<?php

namespace MyQ;

final class MyQWGDOGarageDoorState {
    public const MYQ_DOOR_STATE_UNKNOWN = -1;
    public const MYQ_DOOR_STATE_OPEN = 1;
    public const MYQ_DOOR_STATE_CLOSED = 2;
    public const MYQ_DOOR_STATE_OPENING = 4;
    public const MYQ_DOOR_STATE_CLOSING = 5;

    protected $state = self::MYQ_DOOR_STATE_UNKNOWN;
    protected $state_time = 0;

    protected $state_descriptions = [
        self::MYQ_DOOR_STATE_UNKNOWN => 'unknown',
        self::MYQ_DOOR_STATE_OPEN => 'open',
        self::MYQ_DOOR_STATE_CLOSED => 'closed',
        self::MYQ_DOOR_STATE_OPENING => 'opening',
        self::MYQ_DOOR_STATE_CLOSING => 'closing',
    ];

    public function __construct($state = false, $timestamp = false) {
        if ($state) {
            $this->state = $state;
        }
        $this->state_time = time();
        if ($timestamp) {
            $this->state_time = (int)$timestamp / 1000;
        }
        return $this;
    }

    public function getDescription() {
        return $this->state_descriptions[$this->state];
    }

    public function getTime() {
        return $this->state_time;
    }

    public function getDeltaInt() {
        date_default_timezone_set('UTC');
        return (int)floor(time() - $this->state_time);
    }

    public function getDeltaStr() {
        $delta = $this->getDeltaInt();

        // Convert delta in human-readable format
        // via https://stackoverflow.com/a/43956977/98030
        $secondsInAMinute = 60;
        $secondsInAnHour = 60 * $secondsInAMinute;
        $secondsInADay = 24 * $secondsInAnHour;

        // Extract days
        $days = floor($delta / $secondsInADay);

        // Extract hours
        $hourSeconds = $delta % $secondsInADay;
        $hours = floor($hourSeconds / $secondsInAnHour);

        // Extract minutes
        $minuteSeconds = $hourSeconds % $secondsInAnHour;
        $minutes = floor($minuteSeconds / $secondsInAMinute);

        // Extract the remaining seconds
        $remainingSeconds = $minuteSeconds % $secondsInAMinute;
        $seconds = ceil($remainingSeconds);

        // Format and return
        $timeParts = [];
        $sections = [
            'day' => (int)$days,
            'hour' => (int)$hours,
            'minute' => (int)$minutes,
            'second' => (int)$seconds,
        ];

        foreach ($sections as $name => $value) {
            if ($value > 0) {
                $timeParts[] = $value . ' ' . $name . ($value == 1 ? '' : 's');
            }
        }

        return sizeof($timeParts) ? implode(', ', $timeParts) : '0 seconds';
    }

    public function __toString() {
        return "{$this->getDescription()} for {$this->getDeltaStr()}";
    }
}
