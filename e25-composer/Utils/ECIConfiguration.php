<?php

namespace E25\Composer\Utils;

use Composer\Composer;

class ECIConfiguration
{
    private static $instance;

    private $configuration;

    private $dot_env_config;

    public static function createInstance(Composer $composer)
    {
        self::$instance = new self($composer);
    }

    public static function getInstance()
    {
        return self::$instance;
    }

    private function loadDotEnvConfig(Composer $composer)
    {
        $sep = DIRECTORY_SEPARATOR;
        $dot_env_file_content = '';
        $dot_env_path_1 = implode('', [
            str_replace('vendor', '', $composer->getConfig()->get('vendor-dir')),
            'docker',
            $sep,
            '.env'
        ]);
        // Project setup withing tmp directory in legolas
        $dot_env_path_2 = implode('', [
            str_replace('vendor', '', $composer->getConfig()->get('vendor-dir')),
            '..',
            $sep,
            'docker',
            $sep,
            '.env'
        ]);
        if (!file_exists($dot_env_path_1) && !file_exists($dot_env_path_2)) {
            $this->dot_env_config = [];
        } elseif (file_exists($dot_env_path_1)) {
            $dot_env_file_content = file_get_contents($dot_env_path_1);
        } elseif (file_exists($dot_env_path_2)) {
            $dot_env_file_content = file_get_contents($dot_env_path_2);
        }
        $this->dot_env_config = [];
        foreach (explode(PHP_EOL, $dot_env_file_content) as $line) {
            if (substr($line, 0, 1) == '#') {
                continue;
            }
            $line_segments = explode('=', $line);
            if (sizeof($line_segments) === 2) {
                [$key, $value] = $line_segments;
                $this->dot_env_config[$key] = $value;
            }
        }
    }

    private function get(string $key, $default = null, $configuration = null)
    {
        $value = $configuration ?? $this->configuration;
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

    public function __construct(Composer $composer)
    {
        $root_package_extras = $composer->getPackage()->getExtra();
        $this->configuration = array_key_exists(
            'e25-composer-installer',
            $root_package_extras
        )
            ? $root_package_extras['e25-composer-installer']
            : [];
        $this->loadDotEnvConfig($composer);
    }


    public function shouldInstallPrivatePackages()
    {
        $enable_core_file_skipping = $this->get(
            'ignore_e25_packages.enable',
            false
        );
        $enable_core_file_skipping = filter_var(
            $this->get(
                'SKIP_E25_COMPOSER_PACKAGE_INSTALLATION',
                $enable_core_file_skipping,
                $this->dot_env_config
            ),
            FILTER_VALIDATE_BOOLEAN
        );
        return !$enable_core_file_skipping;
    }

    public function isIgnoredPackage($package_name)
    {
        $is_ignored = false;
        foreach ($this->get('ignore_e25_packages.match', []) as $pattern) {
            if (preg_match($pattern, $package_name) === 1) {
                $is_ignored = true;
                break;
            }
        }
        return $is_ignored;
    }
}
