<?php
namespace E25\ComposerInstaller;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

class Plugin implements PluginInterface
{
    protected $composer;
    protected $io;

    public function activate(Composer $composer, IOInterface $io) : void
    {
        $installer = new Installer($io, $composer);
        $composer->getInstallationManager()->addInstaller($installer);
    }

    public function deactivate(Composer $composer, IOInterface $io) : void
    {
        //
    }

    public function uninstall(Composer $composer, IOInterface $io) : void
    {
        //
    }
}
