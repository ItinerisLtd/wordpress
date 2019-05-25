<?php
declare(strict_types=1);

namespace Composer\Itineris\WordPress;

class Release
{
    /** @var string */
    protected $name;
    /** @var string */
    protected $version;
    /** @var array */
    protected $require;
    /** @var array */
    protected $dist;

    public function __construct(string $name, string $version, array $dist, array $require)
    {
        $this->name = $name;
        $this->version = $version;
        $this->dist = $dist;
        $this->require = $require;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'version' => $this->version,
            'dist' => $this->dist,
            'require' => $this->require,
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
    }
}
