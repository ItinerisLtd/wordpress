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
        $version = $this->parseVersion($downloadUrl);
        if (null === $version) {
            return null;
        }

        return new Release(
            'itinerisltd/wordpress',
            $version,
            [
                'type' => 'zip',
                'url' => $downloadUrl,
            ],
            $this->requirementCollection->forWordPressCore($version)
        );
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
