<?php


namespace AppBundle\Util;

class DateUtils
{
    const INTERVAL_DAILY = 'daily';
    const INTERVAL_MONTHLY = 'monthly';
    const INTERVAL_YEARLY = 'yearly';

    public static function getIntervalFormat($interval)
    {
        $supportedIntervals = [
            self::INTERVAL_DAILY => '%Y-%m-%d',
            self::INTERVAL_MONTHLY => '%Y-%m',
            self::INTERVAL_YEARLY=> '%Y',
        ];

        if (!isset($supportedIntervals[$interval])) {
            throw new \LogicException(sprintf('Unknown date interval: %s', $interval));
        }

        return $supportedIntervals[$interval];
    }
}
