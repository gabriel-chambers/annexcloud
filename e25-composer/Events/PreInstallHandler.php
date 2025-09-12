<?php
namespace E25\Composer\Events;

use Composer\Script\Event;
use E25\Composer\Utils\ECIConfiguration;
use E25\Composer\Downloader\FakeDownloader;
use E25\Composer\Overrides\ZipDownloader;
use E25\Composer\Config\Constants;

class PreInstallHandler
{
    public static function handle(Event $event)
    {
        $composer =  $event->getComposer();
        $repository_manager = $composer->getRepositoryManager();
        $repository_manager->setRepositoryClass('composer', 'E25\Composer\Overrides\ComposerRepository');

        ECIConfiguration::createInstance($composer);
        $e25_composer_installer_config = ECIConfiguration::getInstance();
        $install_private_packages
            = $e25_composer_installer_config->shouldInstallPrivatePackages();
        if ($install_private_packages) {
            $io = $event->getIo();
            $config = $composer->getConfig();
            $composer->getDownloadManager()->setDownloader(
                Constants::FAKE_DIST_TYPE,
                new ZipDownloader(
                    $io,
                    $config,
                    $composer->getLoop()->getHttpDownloader(),
                    $composer
                )
            );
        } else {
            $composer->getDownloadManager()->setDownloader(
                Constants::FAKE_DIST_TYPE,
                new FakeDownloader()
            );
        }

        $repository_manager->addRepository(
            $repository_manager->createRepository(
                'composer',
                [
                    'type' => 'composer',
                    'url' => $install_private_packages
                        ? Constants::PRIVATE_REPO_URL
                        : 'https://packagist.org',
                ]
            )
        );
    }
}
