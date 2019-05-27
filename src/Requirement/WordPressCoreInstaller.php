<?php
declare(strict_types=1);

namespace Composer\Itineris\WordPress\Requirement;

class WordPressCoreInstaller implements RequirementInterface
{
    public function getPackageName(): string
    {
        return 'roots/wordpress-core-installer';
    }

    public function forWordPressCore(string $version): string
    {
        return '>=1.1.0';
    }
}
