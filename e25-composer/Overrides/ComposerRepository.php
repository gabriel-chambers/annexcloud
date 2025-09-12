<?php
namespace E25\Composer\Overrides;

use Composer\Package\Package;
use Composer\Repository\ComposerRepository as BaseRepository;
use E25\Composer\Utils\ECIConfiguration;
use E25\Composer\Config\Constants;

class ComposerRepository extends BaseRepository
{
    private $private_repo_names;

    public function loadPackages(
        array $packageNameMap,
        array $acceptableStabilities,
        array $stabilityFlags,
        array $alreadyLoaded = []
    ) {
        $e25_composer_installer_config = ECIConfiguration::getInstance();
        $private_repo_names = array_filter(
            $packageNameMap,
            function ($package_name) use ($e25_composer_installer_config) {
                return !$e25_composer_installer_config->shouldInstallPrivatePackages()
                && $e25_composer_installer_config->isIgnoredPackage($package_name);
            },
            ARRAY_FILTER_USE_KEY
        );
        array_walk(
            $private_repo_names,
            function (&$value, $package_name) use ($e25_composer_installer_config) {
                if ($e25_composer_installer_config->isIgnoredPackage($package_name)) {
                    $value = new Package(
                        $package_name,
                        $value->getVersion(),
                        $value->getPrettyString()
                    );
                    $value->setDistType(Constants::FAKE_DIST_TYPE);
                    $value->setDistUrl(Constants::FAKE_DIST_URL);
                }
            }
        );
        if (is_null($this->private_repo_names)) {
            $this->private_repo_names = $private_repo_names;
        }
        $locally_resolved_package_names = array_keys($private_repo_names);

        $remote_resolve_results = parent::loadPackages(
            array_filter(
                $packageNameMap,
                function ($package_name) use ($locally_resolved_package_names) {
                    return !in_array($package_name, $locally_resolved_package_names);
                },
                ARRAY_FILTER_USE_KEY
            ),
            $acceptableStabilities,
            $stabilityFlags,
            $alreadyLoaded
        );

        if (isset($remote_resolve_results['packages'])) {
            array_walk(
                $remote_resolve_results['packages'],
                function (&$value) use ($e25_composer_installer_config) {
                    if ($e25_composer_installer_config->shouldInstallPrivatePackages()
                        && method_exists($value, 'getName')
                        && $e25_composer_installer_config->isIgnoredPackage($value->getName())
                        && $value->getNotificationUrl()
                    ) {
                        $value->setNotificationUrl('');
                    }
                }
            );
        }
        return [
            'namesFound' => array_merge(
                isset($remote_resolve_results['namesFound']) ? $remote_resolve_results['namesFound'] : [],
                $locally_resolved_package_names
            ),
            'packages' => array_merge(
                isset($remote_resolve_results['packages']) ? $remote_resolve_results['packages'] : [],
                array_values($private_repo_names)
            )
        ];
    }
}
