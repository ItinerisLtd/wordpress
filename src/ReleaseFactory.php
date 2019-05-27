<?php
declare(strict_types=1);

namespace Composer\Itineris\WordPress;

use Composer\Itineris\WordPress\Requirement\RequirementCollection;
use Composer\Semver\VersionParser;
use UnexpectedValueException;

class ReleaseFactory
{
    protected const VERSION_PATTERN = '/\S+\/wordpress-(?<version>\S+[^IIS])(-IIS)?\.(zip|tar\.gz)/';
    /** @var VersionParser */
    protected $versionParser;
    /** @var RequirementCollection */
    protected $requirementCollection;

    public function __construct(VersionParser $versionParser, RequirementCollection $requirementCollection)
    {
        $this->versionParser = $versionParser;
        $this->requirementCollection = $requirementCollection;
    }

    public static function make(): self
    {
        return new static(
            new VersionParser(),
            RequirementCollection::make()
        );
    }

    public function build(string $downloadUrl): ?Release
    {
        $name = $this->parseName($downloadUrl);
        $dist = $this->parseDist($downloadUrl);
        $version = $this->parseVersion($downloadUrl);

        // in_array is short. However, PHPStan doesn't understand `in_array`.
        if (null === $name || null === $dist || null === $version) {
            return null;
        }

        return new Release(
            $name,
            $version,
            $dist,
            $this->requirementCollection->forWordPressCore($version)
        );
    }

    protected function parseName(string $url): ?string
    {
        $name = null;
        if (Str::endsWith($url, '.zip')) {
            $name = 'itinerisltd/wordpress';
        }
        if (Str::endsWith($url, '.tar.gz')) {
            $name = 'itinerisltd/wordpress-tar';
        }

        return $name;
    }

    protected function parseDist(string $url): ?array
    {
        $dist = null;
        if (Str::endsWith($url, '.zip')) {
            $dist = [
                'type' => 'zip',
                'url' => $url,
            ];
        } elseif (Str::endsWith($url, '.tar.gz')) {
            $dist = [
                'type' => 'tar',
                'url' => $url,
            ];
        }

        return $dist;
    }

    protected function parseVersion(string $url): ?string
    {
        preg_match(static::VERSION_PATTERN, $url, $matches);
        $version = (string) ($matches['version'] ?? '');

        return $this->isVersion($version) ? $version : null;
    }

    protected function isVersion(string $string): bool
    {
        try {
            $this->versionParser->normalize($string);

            return true;
        } catch (UnexpectedValueException $exception) {
            return false;
        }
    }
}
