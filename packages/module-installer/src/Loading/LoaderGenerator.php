<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Loading;

use Composer\Composer;
use LogicException;
use Modette\ModuleInstaller\Files\File;
use Modette\ModuleInstaller\Files\FileIO;
use Modette\ModuleInstaller\Package\ConfigurationValidator;
use Modette\ModuleInstaller\Utils\PathResolver;
use UnexpectedValueException;

final class LoaderGenerator
{

	public function generateLoader(Composer $composer): void
	{
		$pathResolver = new PathResolver($composer);
		$packages = $composer->getRepositoryManager()->getLocalRepository()->getCanonicalPackages();
		$excluded = $composer->getPackage()->getExtra()['modette']['excluded'] ?? [];

		// Filter out ignored packages and packages without modette.neon
		foreach ($packages as $key => $package) {
			// Package ignored by config
			if (in_array($package->getName(), $excluded, true)) {
				unset($packages[$key]);
			}

			// Ignore packages without modette.neon
			if (!file_exists($pathResolver->getConfigFileFqn($package))) {
				unset($packages[$key]);
			}
		}

		//TODO - sort packages by priority - https://github.com/modette/modette/issues/17
		// composer show --tree
		// https://github.com/schmittjoh/composer-deps-analyzer
		// https://github.com/bulton-fr/dependency-tree
		// $packages = $this->>sortPackagesByPriority($packages);

		$io = new FileIO();
		$validator = new ConfigurationValidator();
		$configFiles = [];

		foreach ($packages as $package) {
			$packageDirRelative = $pathResolver->getRelativePath($package);
			$configFile = $pathResolver->getConfigFileFqn($package);
			$configuration = $validator->validateConfiguration($package->getName(), File::DEFAULT_NAME, $io->read($configFile));

			foreach ($configuration->getFiles() as $fileConfiguration) {
				$configFiles[] = $packageDirRelative . '/' . $fileConfiguration->getFile();
			}
		}

		$io = new FileIO();
		$io->write($this->getLoaderFilePath($composer), $configFiles);
	}

	private function getLoaderFilePath(Composer $composer): string
	{
		$pathResolver = new PathResolver($composer);
		$extra = $composer->getPackage()->getExtra();
		$pluginConfig = $extra['modette'] ?? [];

		if (!isset($pluginConfig['modules'])) {
			throw new LogicException('composer.json key extra.modette.modules must be defined when modette/module-installer is enabled.');
		}

		$configFileRelative = $pluginConfig['modules'];
		$configFile = $pathResolver->getRootDir() . '/' . $configFileRelative;

		if (!file_exists($configFile)) {
			throw new UnexpectedValueException(sprintf(
				'composer.json key extra.modette.modules must be a valid relative path to a config file. Given path is %s, which resolved into %s',
				$configFileRelative,
				$configFile
			));
		}

		return $configFile;
	}

}
