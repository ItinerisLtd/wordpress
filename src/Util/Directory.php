<?php
declare(strict_types=1);

namespace Composer\Itineris\WordPress\Util;

use Symfony\Component\Filesystem\Filesystem;

class Directory
{
    public static function mktemp(Filesystem $filesystem): string
    {
        $path = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR
            . uniqid('wordpress', true);

        $filesystem->mkdir($path);

        return $path;
    }
}
