<?php

declare(strict_types=1);

namespace app\helpers;

final class Money
{
    public const EXPENSE = 'expense';
    public const INCOME = 'income';
    public const TRANSFER = 'transfer';
    public const NEUTRAL = 'neutral';

    private const THIN_NBSP = "\u{202F}";
    private const MINUS = '−';

    public static function digits(int $value): string
    {
        return number_format(abs($value), 0, '', self::THIN_NBSP);
    }

    /**
     * Plain text: "12 088 ₽", "−390 ₽".
     */
    public static function plain(int $value): string
    {
        return ($value < 0 ? self::MINUS : '') . self::digits($value) . "\u{00A0}₽";
    }

    /**
     * Styled amount. Expense always shows a minus, income a plus; neutral keeps the real sign.
     */
    public static function html(int $value, string $kind = self::NEUTRAL, string $extraClass = ''): string
    {
        $sign = match ($kind) {
            self::EXPENSE => self::MINUS,
            self::INCOME => '+',
            self::NEUTRAL => $value < 0 ? self::MINUS : '',
            default => '',
        };
        $class = trim("amount amount--{$kind} {$extraClass}");

        return '<span class="' . $class . '">' . $sign . self::digits($value)
            . '<span class="amount__cur">₽</span></span>';
    }
}
