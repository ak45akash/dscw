<?php

namespace App\Support;

use InvalidArgumentException;

class Duration
{
    public const MIN_MINUTES = 15;

    public const MAX_MINUTES = 20160; // 14 days

    /**
     * @return array{days: int, hours: int, minutes: int}
     */
    public static function toParts(int $totalMinutes): array
    {
        $totalMinutes = max(0, $totalMinutes);

        return [
            'days' => intdiv($totalMinutes, 24 * 60),
            'hours' => intdiv($totalMinutes % (24 * 60), 60),
            'minutes' => $totalMinutes % 60,
        ];
    }

    public static function fromParts(int $days = 0, int $hours = 0, int $minutes = 0): int
    {
        if ($days < 0 || $hours < 0 || $minutes < 0) {
            throw new InvalidArgumentException('Duration parts cannot be negative.');
        }

        if ($hours > 23) {
            throw new InvalidArgumentException('Hours must be between 0 and 23.');
        }

        if ($minutes > 59) {
            throw new InvalidArgumentException('Minutes must be between 0 and 59.');
        }

        return ($days * 24 * 60) + ($hours * 60) + $minutes;
    }

    public static function format(int $totalMinutes): string
    {
        $parts = self::toParts($totalMinutes);
        $labels = [];

        if ($parts['days'] > 0) {
            $labels[] = $parts['days'].' day'.($parts['days'] === 1 ? '' : 's');
        }

        if ($parts['hours'] > 0) {
            $labels[] = $parts['hours'].' hour'.($parts['hours'] === 1 ? '' : 's');
        }

        if ($parts['minutes'] > 0) {
            $labels[] = $parts['minutes'].' minute'.($parts['minutes'] === 1 ? '' : 's');
        }

        return $labels !== [] ? implode(', ', $labels) : '0 minutes';
    }
}
