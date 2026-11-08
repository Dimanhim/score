<?php

declare(strict_types=1);

namespace app\helpers;

final class Dates
{
    private const MONTHS = ['янв', 'фев', 'мар', 'апр', 'мая', 'июн', 'июл', 'авг', 'сен', 'окт', 'ноя', 'дек'];

    /**
     * "7 ноя" for the current year, "12 окт 2025" otherwise.
     */
    public static function short(int $ts): string
    {
        $label = date('j', $ts) . ' ' . self::MONTHS[(int) date('n', $ts) - 1];

        return date('Y', $ts) === date('Y') ? $label : $label . ' ' . date('Y', $ts);
    }

    /**
     * "Сегодня, 7 ноя", "Вчера, 6 ноя", "5 ноя".
     */
    public static function day(int $ts): string
    {
        $day = date('Y-m-d', $ts);

        return match ($day) {
            date('Y-m-d') => 'Сегодня, ' . self::short($ts),
            date('Y-m-d', strtotime('yesterday')) => 'Вчера, ' . self::short($ts),
            default => self::short($ts),
        };
    }
}
