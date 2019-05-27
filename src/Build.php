<?php
declare(strict_types=1);

namespace Composer\Itineris\WordPress;

use Composer\IO\IOInterface;
use Composer\IO\NullIO;
use Composer\Itineris\WordPress\Util\Directory;
use Composer\Script\Event;
use Composer\Util\RemoteFilesystem;
use Cz\Git\GitRepository;
use RuntimeException;
use Symfony\Component\Filesystem\Filesystem;

// TODO: Refactor!
class Build
{
    // TODO!
    protected const GIT_REPO_URL = 'git@github.com:ItinerisLtd/wordpress-testing.git';
    /** @var IOInterface */
    protected $io;
    /** @var DownloadUrlRepo */
    protected $downloadUrlRepo;
    /** @var ReleaseFactory */
    protected $releaseFactory;
    /** @var Filesystem */
    protected $filesystem;
    /** @var License */
    protected $license;

    public function __construct(
        IOInterface $io,
        DownloadUrlRepo $downloadUrlRepo,
        ReleaseFactory $releaseFactory,
        Filesystem $filesystem,
        License $license
    ) {
        $this->io = $io;
        $this->downloadUrlRepo = $downloadUrlRepo;
        $this->releaseFactory = $releaseFactory;
        $this->filesystem = $filesystem;
        $this->license = $license;
    }

    public static function run(Event $event): void
    {
        $self = static::make(
            $event->getIO()
        );
        $self->execute();
    }

    public static function make(IOInterface $io): self
    {
        $rfs = new RemoteFilesystem(
            new NullIO()
        );

        return new static(
            $io,
            DownloadUrlRepo::make($rfs),
            ReleaseFactory::make($rfs),
            new Filesystem(),
            License::make()
        );
    }

    public function execute(): void
    {
        $this->io->writeError('Create temporary directory', false);
        $this->io->writeError(' <comment>creating</comment>', false);
        $temporaryDirectoryPath = Directory::mktemp($this->filesystem);
        $this->io->overwriteError(" <comment>${temporaryDirectoryPath}</comment>");

        $this->io->writeError('Clone git repository <comment>' . static::GIT_REPO_URL . '</comment>');
        $repo = GitRepository::cloneRepository(static::GIT_REPO_URL, $temporaryDirectoryPath);

        $this->io->writeError('Fetch git remote <comment>origin</comment>');
        $repo->fetch('origin');

        $this->io->writeError('List git tags ', false);
        $this->io->writeError('<comment>counting</comment>', false);
        $tags = (array) $repo->getTags();
        $this->io->overwriteError('<comment>' . count($tags) . ' tag(s) found</comment>');

        $this->io->writeError(
            'Fetch WordPress core release information from ' . DownloadUrlRepo::RELEASE_FEED_URL . ' ',
            false
        );
        $this->io->writeError('<comment>fetching</comment>', false);
        $downloadUrls = $this->downloadUrlRepo->all();
        $this->io->overwriteError('<comment>' . count($downloadUrls) . ' release(s) found</comment>');

        $this->io->writeError('Compare WordPress core releases with git repository tags ', false);
        $this->io->writeError('<comment>comparing</comment>', false);
        $newVersions = array_diff(
            array_keys($downloadUrls),
            $tags
        );
        $this->io->overwriteError('<comment>' . count($newVersions) . ' new release(s) found</comment>');

        if ([] === $newVersions) {
            $this->io->overwriteError('<info>Skip: Git repository is up to date</info>');
            return;
        }

        $this->io->writeError('Update git repository');
        array_map(function (string $version) use ($repo, $temporaryDirectoryPath, $downloadUrls): void {
            $this->buildVersion($version, $repo, $temporaryDirectoryPath, $downloadUrls[$version]);
        }, $newVersions);

        $this->io->overwriteError('<info>Success: Done</info>');
    }

    public function buildVersion(
        string $version,
        GitRepository $repo,
        string $temporaryDirectoryPath,
        string $downloadUrl
    ): void {
        $this->io->writeError("Build WordPress core v${version} ", false);

        $this->io->writeError("<comment>checking out orphan branch $version</comment>", false);
        $repo->execute([
            'checkout',
            '--orphan',
            $version,
        ]);

        $this->io->overwriteError('<comment>writing LICENSE</comment>', false);
        $this->filesystem->dumpFile(
            "${temporaryDirectoryPath}/LICENSE",
            $this->license->getContent()
        );

        $this->io->overwriteError('<comment>writing composer.json</comment>', false);
        $release = $this->releaseFactory->build($version, $downloadUrl);
        if (null === $release) {
            throw new RuntimeException("Unable to build v${version} from ${downloadUrl}");
        }

        $composerJsonContent = json_encode(
            $release->toArray(),
            JSON_PRETTY_PRINT
        );
        if (false === $composerJsonContent) {
            throw new RuntimeException('Failed to encode composer.json');
        }
        $this->filesystem->dumpFile("$temporaryDirectoryPath/composer.json", $composerJsonContent . PHP_EOL);

        $this->io->overwriteError('<comment>adding files into git</comment>', false);
        $repo->addFile('LICENSE', 'composer.json');

        $this->io->overwriteError('<comment>committing files into git</comment>', false);
        $repo->commit("Version bump ${version}");

        $this->io->overwriteError("<comment>tagging ${version}</comment>", false);
        $repo->createTag($version, [
            '--annotate',
            '--message' => "Version bump ${version}",
        ]);
        $this->io->overwriteError('<comment>pushing to git remote</comment>', false);
        $repo->push('origin', [
            'Head',
            '--follow-tags',
        ]);
        $this->io->overwriteError('<comment>done</comment>');
    }
}
