<?php
namespace E25\Composer\Overrides;

use Composer\Composer;
use Composer\Config;
use Composer\Cache;
use Composer\Downloader\ZipDownloader as Base;
use Composer\Package\PackageInterface;
use React\Promise\PromiseInterface;
use Composer\IO\IOInterface;
use Composer\Util\HttpDownloader;
use Composer\EventDispatcher\EventDispatcher;
use Composer\Util\ProcessExecutor;
use Composer\Semver\Constraint\Constraint;
use E25\Composer\Config\Constants;
use E25\Composer\Utils\ECIConfiguration;

class ZipDownloader extends Base
{
    private $composer;

    /**
     * @inheritDoc
     */
    public function __construct(IOInterface $io, Config $config, HttpDownloader $httpDownloader, Composer $composer, ?EventDispatcher $eventDispatcher = null, ?Cache $cache = null, ?Filesystem $filesystem = null, ?ProcessExecutor $process = null)
    {
        $this->composer = $composer;
        parent::__construct($io, $config, $httpDownloader, $eventDispatcher, $cache, $filesystem, $process);
    }


    /**
     * @inheritDoc
     */
    public function download(PackageInterface $package, string $path, ?PackageInterface $prevPackage = null, bool $output = true): PromiseInterface
    {
        $e25_composer_installer_config = ECIConfiguration::getInstance();
        if ($e25_composer_installer_config->isIgnoredPackage($package->getName())
            && $package->getDistUrl() == Constants::FAKE_DIST_URL
        ) {
            $package_found = $this->composer->getRepositoryManager()
                ->findPackage(
                    $package->getName(),
                    new Constraint(Constraint::STR_OP_EQ_ALT, $package->getVersion())
                );
            if (!$package_found) {
                return \React\Promise\resolve(null);
            } elseif ($package->getDistUrl() != $package_found->getDistUrl()) {
                return $this->composer->getInstallationManager()
                    ->getInstaller($package_found->getType())
                    ->download($package_found, $prevPackage);
                //     ->getInstallPath($package_found);
                // $package->setDistType($package_found->getDistType());
                // $package->setDistUrl($package_found->getDistUrl());
                // $package->setDistReference($package_found->getDistReference());
                // $package->setDistMirrors($package_found->getDistMirrors());
            }
        }
        return parent::download($package, $path, $prevPackage, $output);
    }

    protected function extract(PackageInterface $package, string $file, string $path): PromiseInterface
    {
        $e25_composer_installer_config = ECIConfiguration::getInstance();
        if ($e25_composer_installer_config->isIgnoredPackage($package->getName())
            && $package->getDistUrl() == Constants::FAKE_DIST_URL
        ) {
            return \React\Promise\resolve(null);
        }
        return $this->extract($package, $file, $path);
    }
}
