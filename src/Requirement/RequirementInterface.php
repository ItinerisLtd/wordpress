<?php
declare(strict_types=1);

namespace Composer\Itineris\WordPress\Requirement;

interface RequirementInterface
{
    public function getPackageName(): string;

    public function forWordPressCore(string $version): string;
}
