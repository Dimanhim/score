<?php

declare(strict_types=1);

namespace app\helpers;

/**
 * Lucide icon from the inline sprite in views/layouts/_icons.php.
 */
final class Icon
{
    public static function svg(string $name, string $class = ''): string
    {
        $class = trim('icon ' . $class);

        return '<svg class="' . $class . '" aria-hidden="true"><use href="#i-' . $name . '"/></svg>';
    }
}
