<?php
declare(strict_types=1);

namespace Composer\Itineris\WordPress\Requirement;

use Codeception\Test\Unit;

class WordPressCoreInstallerTest extends Unit
{
    public function testForWordPressCore()
    {
        $wordPressCoreInstaller = new WordPressCoreInstaller();

        $actual = $wordPressCoreInstaller->forWordPressCore('xyz');

        $this->assertSame('>=1.1.0', $actual);
    }

    public function testGetPackageName()
    {
        $wordPressCoreInstaller = new WordPressCoreInstaller();

        $actual = $wordPressCoreInstaller->getPackageName();

        $this->assertSame('roots/wordpress-core-installer', $actual);
    }
}
