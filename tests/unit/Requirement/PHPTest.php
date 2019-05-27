<?php
declare(strict_types=1);

namespace Composer\Itineris\WordPress\Requirement;

use Codeception\Test\Unit;

class PHPTest extends Unit
{
    /**
     * @dataProvider versionProvider
     */
    public function testForWordPressCore(string $version, string $expected)
    {
        $php = new PHP();

        $actual = $php->forWordPressCore($version);

        $this->assertSame($expected, $actual);
    }

    public function versionProvider(): array
    {
        return [
            ['5.2.1', '>=5.6.20'],
            ['5.2.1-RC1', '>=5.6.20'],
            ['5.2', '>=5.6.20'],
            ['5.2-RC1', '>=5.6.20'],
            ['5.2-beta1', '>=5.6.20'],
            ['5.1.1', '>=5.2.4'],
            ['5.1.1-RC1', '>=5.2.4'],
            ['5.1.1-beta1', '>=5.2.4'],
            ['5.1', '>=5.2.4'],
            ['5.1-RC1', '>=5.2.4'],
            ['5.1-beta1', '>=5.2.4'],
        ];
    }

    public function testGetPackageName()
    {
        $php = new PHP();

        $actual = $php->getPackageName();

        $this->assertSame('php', $actual);
    }
}
