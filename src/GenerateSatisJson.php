<?php
declare(strict_types=1);

namespace Composer\Itineris\WordPress;

use Composer\Script\Event;

class GenerateSatisJson
{
    public static function run(Event $event): void
    {
        $io = $event->getIO();
        $satisJson = SatisJson::make($io);

        $io->writeError('Refreshing satis.json with new WordPress releases...');

        $satisJson->generate('satis.json', 'satis.base.json');

        $io->writeError('');
        $io->writeError('<info>Success</info>: Generated new satis.json');
    }
}
