<?php
namespace E25\ComposerInstaller;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Installer\InstallerInterface;
use Composer\Repository\InstalledRepositoryInterface;
use Composer\Package\PackageInterface;
use Composer\Installers\Installer as BaseInstaller;
use Dotenv\Dotenv;

class Installer extends BaseInstaller implements InstallerInterface
{
    const DEFAULT_SUPPORTS = [
        'wordpress-theme',
        'wordpress-plugin',
        'wordpress-bergblock'
    ];
    private $configuration;
    private $dotenv;

    /**
     * Get values from composer extras
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    private function getConfig(string $key, $default = null)
    {
        $value = $this->configuration;
        $key_segments = explode('.', $key);
        foreach ($key_segments as $key_segment) {
            if (array_key_exists($key_segment, $value)) {
                $value = $value[$key_segment];
            } else {
                return $default;
            }
        }
        return $value;
    }

    /**
     * Check whether the package is allowed to install
     *
     * @param PackageInterface $package
     * @return boolean
     */
    private function isAllowedToInstall(PackageInterface $package) : bool
    {
        $enable_core_file_skipping = $this->getConfig(
            'ignore_e25_packages.enable',
            false
        );
        if (isset($_ENV['SKIP_E25_COMPOSER_PACKAGE_INSTALLATION'])) {
            $enable_core_file_skipping = filter_var(
                $_ENV['SKIP_E25_COMPOSER_PACKAGE_INSTALLATION'],
                FILTER_VALIDATE_BOOLEAN
            );
        }
        if (!$enable_core_file_skipping) {
            return true;
        }
        $is_allowed = true;
        $package_name = $package->getName();
        foreach ($this->getConfig('ignore_e25_packages.match', []) as $pattern) {
            if (preg_match($pattern, $package_name) === 1) {
                $is_allowed = false;
                break;
            }
        }
        return $is_allowed;
    }

    /**
     * Load docker/.env to the context
     *
     * @param Composer $composer
     * @return void
     */
    private function loadDotEnv(Composer $composer)
    {
        $this->dotenv = Dotenv::createImmutable(
            $composer->getConfig()->get('vendor-dir') . '/../docker'
        );
        $this->dotenv->safeLoad();
        $this->dotenv->ifPresent('SKIP_E25_COMPOSER_PACKAGE_INSTALLATION')->isBoolean();
    }

    public function __construct(IOInterface $io, Composer $composer)
    {
        parent::__construct($io, $composer);
        $root_package = $composer->getPackage();
        $root_package_extras = $root_package->getExtra();
        $this->configuration = array_key_exists('e25-composer-installer', $root_package_extras)
            ? $root_package_extras['e25-composer-installer']
            : [];
        $this->loadDotEnv($composer);
    }

    /**
     * @inheritDoc
     */
    public function supports($package_type)
    {
        $supports = $this->getConfig('supports', self::DEFAULT_SUPPORTS);
        return in_array($package_type, $supports);
    }

    /**
     * @inheritDoc
     */
    public function isInstalled(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        return $this->isAllowedToInstall($package)
            ? parent::isInstalled($repo, $package)
            : true;
    }

    /**
     * @inheritDoc
     */
    public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        return $this->isAllowedToInstall($package)
            ? parent::install($repo, $package)
            : null;
    }

    /**
     * @inheritDoc
     */
    public function update(InstalledRepositoryInterface $repo,
        PackageInterface $initial,
        PackageInterface $target
    ) {
        return $this->isAllowedToInstall($initial)
            ? parent::update($repo, $initial, $target)
            : null;
    }
}
