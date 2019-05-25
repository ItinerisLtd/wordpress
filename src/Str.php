<?php
declare(strict_types=1);

namespace Composer\Itineris\WordPress;

class Str
{
    public static function endsWith(string $haystack, string $needle): bool
    {
        $length = strlen($needle);
        return substr($haystack, -$length) === $needle;
    }
}
