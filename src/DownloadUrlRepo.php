<?php
declare(strict_types=1);

namespace Composer\Itineris\WordPress;

use Composer\Itineris\WordPress\Util\Url;
use Composer\Semver\VersionParser;
use Composer\Util\RemoteFilesystem;
use RuntimeException;

class DownloadUrlRepo
{
    public const RELEASE_FEED_URL = 'https://wordpress.org/download/releases/';
    protected const KNOWN_DOWNLOAD_URLS = 306; // As of 27 May 2019.
    // phpcs:ignore Generic.Files.LineLength.TooLong
    protected const DOWNLOAD_URL_PATTERN = '/<a[^>]*href="(?<downloadUrl>https:\/\/[\S]+\/wordpress-[4-9]\S+[^IIS]\.zip)\.sha1"[^>]*>/';
    protected const VERSION_PATTERN = '/\S+\/wordpress-(?<version>\S+[^IIS])(-IIS)?\.(zip|tar\.gz)/';

    /** @var RemoteFilesystem */
    protected $rfs;
    /** @var VersionParser */
    protected $versionParser;

    public function __construct(RemoteFilesystem $rfs, VersionParser $versionParser)
    {
        $this->rfs = $rfs;
        $this->versionParser = $versionParser;
    }

    public static function make(RemoteFilesystem $rfs): self
    {
        return new static($rfs, new VersionParser());
    }

    public function all(): array
    {
        $matches = [];

        $html = (string) $this->rfs->getContents(
            Url::getHost(static::RELEASE_FEED_URL),
            static::RELEASE_FEED_URL
        );

        preg_match_all(static::DOWNLOAD_URL_PATTERN, $html, $matches);
        $downloadUrls = $matches['downloadUrl'] ?? [];
        $downloadUrls = array_unique($downloadUrls);

        $this->failIfDownloadUrlsNotFound($downloadUrls);

        $versions = array_map(function (string $downloadUrl): string {
            return $this->parseVersion($downloadUrl);
        }, $downloadUrls);

        $downloadUrls = array_combine($versions, $downloadUrls);
        if (false === $downloadUrls) {
            throw new RuntimeException(__METHOD__ . '(): Number of elements mismatch');
        }

        $this->failIfDownloadUrlsNotFound($downloadUrls);

        return $downloadUrls;
    }

    protected function failIfDownloadUrlsNotFound(array $downloadUrls): void
    {
        $count = count($downloadUrls);

        if ($count >= static::KNOWN_DOWNLOAD_URLS) {
            return;
        }

        $message = sprintf('Only %1$d package URL(s) found on %2$s', $count, static::RELEASE_FEED_URL);
        throw new RuntimeException($message);
    }

    protected function parseVersion(string $url): string
    {
        preg_match(static::VERSION_PATTERN, $url, $matches);
        $version = (string) ($matches['version'] ?? '');

        // Throw UnexpectedValueException if $version is malformed.
        $this->versionParser->normalize($version);

        return $version;
    }
}
