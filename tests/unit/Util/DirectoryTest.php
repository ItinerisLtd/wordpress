<?php
declare(strict_types=1);

namespace Composer\Itineris\WordPress\Util;

use Codeception\Test\Unit;
use Mockery;
use Symfony\Component\Filesystem\Filesystem;

class DirectoryTest extends Unit
{
    public function testMktemp()
    {
        $filesystem = Mockery::mock(Filesystem::class);

        $filesystem->shouldReceive('mkdir')
            ->withArgs(function ($path) {
                $needle = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'wordpress';
                return (0 === strpos($path, $needle));
            });

        Directory::mktemp($filesystem);
    }
}
