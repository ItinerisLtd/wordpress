<?php
declare(strict_types=1);

namespace Composer\Itineris\WordPress;

use Codeception\Test\Unit;

class ReleaseTest extends Unit
{
    public function testToArray()
    {
        $release = new Release(
            'my-vendor/my-package',
            '1.2.3-beta4',
            [
                'type' => 'my-type',
                'url' => 'https://example.test/abc/xyz.zip',
                'shasum' => 'my-shasum',
            ],
            [
                'php' => '^2.2.3',
                'your-vendor/your-package' => '>=3.2.3-beta4',
            ]
        );

        $actual = $release->toArray();

        $expected = [
            'name' => 'my-vendor/my-package',
            'version' => '1.2.3-beta4',
            'dist' => [
                'type' => 'my-type',
                'url' => 'https://example.test/abc/xyz.zip',
                'shasum' => 'my-shasum',
            ],
            'require' => [
                'php' => '^2.2.3',
                'your-vendor/your-package' => '>=3.2.3-beta4',
            ],
            'provide' => [
                'wordpress/core-implementation' => '1.2.3-beta4',
            ],
            'type' => 'wordpress-core',
            'description' => 'WordPress is web software you can use to create a beautiful website or blog.',
            'keywords' => [
                'wordpress',
                'blog',
                'cms',
            ],
            'homepage' => 'http://wordpress.org/',
            'license' => 'GPL-2.0-or-later',
            'authors' => [
                [
                    'name' => 'WordPress Community',
                    'homepage' => 'http://wordpress.org/about/',
                ],
            ],
            'support' => [
                'issues' => 'http://core.trac.wordpress.org/',
                'forum' => 'http://wordpress.org/support/',
                'wiki' => 'http://codex.wordpress.org/',
                'irc' => 'irc://irc.freenode.net/wordpress',
                'source' => 'http://core.trac.wordpress.org/browser',
            ],
        ];

        $this->assertSame($expected, $actual);
    }
}
