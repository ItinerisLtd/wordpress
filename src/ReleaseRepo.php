<?php
declare(strict_types=1);

namespace Composer\Itineris\WordPress;

use Composer\IO\IOInterface;
use Composer\Itineris\WordPress\Util\Url;
use Composer\Util\RemoteFilesystem;
use RuntimeException;

class ReleaseRepo
{
    protected const RELEASE_FEED_URL = 'https://wordpress.org/download/releases/';
    protected const KNOWN_RELEASES = 306; // As of 25 May 2019.
    // phpcs:ignore Generic.Files.LineLength.TooLong
    protected const DOWNLOAD_URL_PATTERN = '/<a[^>]*href="(?<downloadUrl>https:\/\/[\S]+\/wordpress-[4-9]\S+[^IIS]\.zip)\.sha1"[^>]*>/';

    /** @var ReleaseFactory */
    protected $releaseFactory;
    /** @var RemoteFilesystem */
    protected $rfs;

    public function __construct(ReleaseFactory $releaseFactory, RemoteFilesystem $rfs)
    {
        $this->releaseFactory = $releaseFactory;
        $this->rfs = $rfs;
    }

    public static function make(IOInterface $io): self
    {
        $rfs = new RemoteFilesystem($io);

        return new static(
            ReleaseFactory::make($rfs),
            $rfs
        );
    }

    public function all(): array
    {
        $downloadUrls = $this->fetchDownloadUrls();
        return $this->makeReleases($downloadUrls);
    }

    protected function fetchDownloadUrls(): array
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

        return $downloadUrls;
    }

    protected function failIfDownloadUrlsNotFound(array $downloadUrls): void
    {
        $count = count($downloadUrls);

        if ($count >= static::KNOWN_RELEASES) {
            return;
        }

        $message = sprintf('Only %1$d package URL(s) found on %2$s', $count, static::RELEASE_FEED_URL);
        throw new RuntimeException($message);
    }

    protected function makeReleases(array $downloadUrls): array
    {
        $releases = array_map(function (string $downloadUrl): ?Release {
            return $this->releaseFactory->build($downloadUrl);
        }, $downloadUrls);
        $releases = array_filter($releases);

        $this->failIfReleasesCannotBeParsed($releases);

        return $releases;
    }

    protected function failIfReleasesCannotBeParsed(array $releases): void
    {
        $count = count($releases);

        if ($count >= static::KNOWN_RELEASES) {
            return;
        }

        $message = sprintf('Only %1$d release(s) parsed from %2$s', $count, static::RELEASE_FEED_URL);
        throw new RuntimeException($message);
    }
}
