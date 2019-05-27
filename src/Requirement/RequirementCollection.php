<?php
declare(strict_types=1);

namespace Composer\Itineris\WordPress\Requirement;

use RuntimeException;

class RequirementCollection
{
    protected const REQUIREMENTS = [
        PHP::class,
        WordPressCoreInstaller::class,
    ];

    /** @var RequirementInterface[] */
    protected $requirements;

    public function __construct(RequirementInterface ...$requirements)
    {
        $this->requirements = $requirements;
    }

    public static function make(): self
    {
        $requirements = array_map(function (string $klass): RequirementInterface {
            return new $klass();
        }, static::REQUIREMENTS);

        return new static(...$requirements);
    }

    public function forWordPressCore(string $version): array
    {
        $names = array_map(function (RequirementInterface $requirement): string {
            return $requirement->getPackageName();
        }, $this->requirements);

        $versions = array_map(function (RequirementInterface $requirement) use ($version): string {
            return $requirement->forWordPressCore($version);
        }, $this->requirements);

        $require = array_combine($names, $versions);

        if (false === $require) {
            throw new RuntimeException(__METHOD__ . '(): Number of elements mismatch');
        }

        return $require;
    }
}
