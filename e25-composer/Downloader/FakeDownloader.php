<?php
namespace E25\Composer\Downloader;

use Composer\Downloader\DownloaderInterface;
use Composer\Package\PackageInterface;
use React\Promise\PromiseInterface;

class FakeDownloader implements DownloaderInterface
{
    public function getInstallationSource(): string
    {
        return 'dist';
    }

    public function download(PackageInterface $package, string $path, ?PackageInterface $prevPackage = null): PromiseInterface
    {
        return \React\Promise\resolve(null);
    }

    public function prepare(string $type, PackageInterface $package, string $path, ?PackageInterface $prevPackage = null): PromiseInterface
    {
        return \React\Promise\resolve(null);
    }

    public function install(PackageInterface $package, string $path): PromiseInterface
    {
        return \React\Promise\resolve(null);
    }

    public function update(PackageInterface $initial, PackageInterface $target, string $path): PromiseInterface
    {
        return \React\Promise\resolve(null);
    }

    public function remove(PackageInterface $package, string $path): PromiseInterface
    {
        return \React\Promise\resolve(null);
    }

    public function cleanup(string $type, PackageInterface $package, string $path, ?PackageInterface $prevPackage = null): PromiseInterface
    {
        return \React\Promise\resolve(null);
    }
}
