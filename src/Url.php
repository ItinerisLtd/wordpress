<?php
declare(strict_types=1);

namespace Composer\Itineris\WordPress;

class Url
{
    public static function getHost(string $url): string
    {
        return (string) parse_url($url, PHP_URL_HOST);
    }
}
