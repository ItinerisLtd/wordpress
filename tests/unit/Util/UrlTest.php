<?php
declare(strict_types=1);

namespace Composer\Itineris\WordPress\Util;

use Codeception\Test\Unit;

class UrlTest extends Unit
{
    /**
     * @dataProvider urlProvider
     */
    public function testGetHost(string $url, string $expected)
    {
        $actual = Url::getHost($url);

        $this->assertSame($expected, $actual);
    }

    public function urlProvider(): array
    {
        return [
            ['https://wordpress.org', 'wordpress.org'],
            ['https://wordpress.org/abc.zip', 'wordpress.org'],
            ['https://wordpress.org/abc/xyz.zip', 'wordpress.org'],
            ['https://wordpress.org/abc/xyz.zip.sha1', 'wordpress.org'],
            ['https://downloads.wordpress.org', 'downloads.wordpress.org'],
            ['https://downloads.wordpress.org/abc.zip', 'downloads.wordpress.org'],
            ['https://downloads.wordpress.org/abc/xyz.zip', 'downloads.wordpress.org'],
            ['https://downloads.wordpress.org/abc/xyz.zip.sha1', 'downloads.wordpress.org'],
        ];
    }
}
