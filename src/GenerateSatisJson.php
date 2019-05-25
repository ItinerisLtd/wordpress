<?php
declare(strict_types=1);

namespace Composer\Itineris\WordPress;

use Composer\Semver\VersionParser;
use Symfony\Component\Filesystem\Filesystem;

class GenerateSatisJson
{
    public static function run(): void
    {
        $versionParser = new VersionParser();
        $releaseFactory = new ReleaseFactory($versionParser);
        $releaseRepo = new ReleaseRepo($releaseFactory);

        $filesystem = new Filesystem();

        $satisJson = new SatisJson($releaseRepo, $filesystem);
        $satisJson->generate(
            'satis.json',
            'satis.base.json'
        );
    }
}
