<?php
declare(strict_types=1);

namespace Composer\Itineris\WordPress;

use Composer\Semver\Semver;

class MinPhpVersion
{
    /**
     * Determine minimum PHP version for WordPress core.
     *
     * @see https://wordpress.org/news/2019/04/minimum-php-version-update/
     * @see http://displaywp.com/wordpress-minimum-php-version/
     *
     * @param string $version WordPress core version.
     *
     * @return string
     */
    public function forWordPressCore(string $version): string
    {
        $minPhpVersion = '5.6.20';

        if (Semver::satisfies($version, '< 5.2-dev')) {
            $minPhpVersion = '5.2.4';
        }

        return $minPhpVersion;
    }
}
