<?php


namespace AppBundle\Util;

use DateTime;

class DateUtils
{
    const INTERVAL_DAY = 'day';
    const INTERVAL_MONTH = 'month';
    const INTERVAL_YEAR = 'year';

    public static function getDbIntervalFormat($interval)
    {
        $supportedIntervals = [
            self::INTERVAL_DAY => '%Y-%m-%d',
            self::INTERVAL_MONTH => '%Y-%m',
            self::INTERVAL_YEAR=> '%Y',
        ];

        if (!isset($supportedIntervals[$interval])) {
            throw new \LogicException(sprintf('Unknown date interval: %s', $interval));
        }

        return $supportedIntervals[$interval];
    }

    public static function getDateTime($dateTimeAsText, $interval)
    {
        if (DateUtils::INTERVAL_MONTH === $interval) {
            $dateTimeAsText .= '-01';
        } elseif (DateUtils::INTERVAL_YEAR === $interval) {
            $dateTimeAsText .= '-01-01';
        }

        return new DateTime($dateTimeAsText, new \DateTimeZone('UTC'));
    }
}
