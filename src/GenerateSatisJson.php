<?php
declare(strict_types=1);

namespace Composer\Itineris\WordPress;

use Composer\Script\Event;
use Composer\Util\RemoteFilesystem;
use Symfony\Component\Filesystem\Filesystem;

class GenerateSatisJson
{
    public static function run(Event $event): void
    {
        $io = $event->getIO();

        $releaseRepo = ReleaseRepo::make($io);
        $filesystem = new Filesystem();
        $satisJson = new SatisJson($releaseRepo, $filesystem);

        $io->writeError('Refreshing satis.json with new WordPress releases...');

        $satisJson->generate('satis.json', 'satis.base.json');

        $io->writeError('');
        $io->writeError('<info>Success</info>: Generated new satis.json');
    }
}
