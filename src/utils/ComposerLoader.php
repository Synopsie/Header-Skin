<?php
declare(strict_types=1);

namespace skin\utils;

use pocketmine\thread\ThreadSafeClassLoader;

class ComposerLoader {

    public static function init(string $pluginPath, ThreadSafeClassLoader $classLoader) : void {
        $classLoader->register();
        $classLoader->addPath('', $pluginPath);
    }

    public static function loadRepositoryAndDependencies(string $repoPath) : void {
        $composerJsonPath = $repoPath . '/composer.json';
        if (!file_exists($composerJsonPath)) {
            throw new \RuntimeException("composer.json not found at " . $composerJsonPath);
        }

        $vendorDir = $repoPath . '/vendor';
        if (!file_exists($vendorDir . '/autoload.php')) {
            throw new \RuntimeException("Composer autoload.php not found in " . $vendorDir);
        }

        $classLoader = new ThreadSafeClassLoader();
        $classLoader->register();
        $classLoader->addPath('', $vendorDir);

        require $vendorDir . '/autoload.php';
    }
}