<?php
declare(strict_types=1);

namespace Composer\Itineris\WordPress;

use Composer\Itineris\WordPress\Requirement\RequirementCollection;
use Composer\Itineris\WordPress\Util\Url;
use Composer\Semver\VersionParser;
use Composer\Util\RemoteFilesystem;
use UnexpectedValueException;

class ReleaseFactory
{
    protected const VERSION_PATTERN = '/\S+\/wordpress-(?<version>\S+[^IIS])(-IIS)?\.(zip|tar\.gz)/';

    /** @var VersionParser */
    protected $versionParser;
    /** @var RequirementCollection */
    protected $requirementCollection;
    /** @var RemoteFilesystem */
    protected $rfs;

    public function __construct(
        VersionParser $versionParser,
        RequirementCollection $requirementCollection,
        RemoteFilesystem $rfs
    ) {
        $this->versionParser = $versionParser;
        $this->requirementCollection = $requirementCollection;
        $this->rfs = $rfs;
    }

    public static function make(RemoteFilesystem $rfs): self
    {
        return new static(
            new VersionParser(),
            RequirementCollection::make(),
            $rfs
        );
    }

    public function build(string $downloadUrl): ?Release
    {
        $version = $this->parseVersion($downloadUrl);
        $shasum = $this->getShasum($downloadUrl);

        if (null === $version || null === $shasum) {
            return null;
        }

        return new Release(
            'itinerisltd/wordpress',
            $version,
            [
                'type' => 'zip',
                'url' => $downloadUrl,
                'shasum' => $shasum,
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

    protected function getShasum(string $url): ?string
    {
        $shasum = $this->rfs->getContents(
            Url::getHost($url),
            $url . '.sha1'
        );

        return is_string($shasum) ? $shasum : null;
    }
}
