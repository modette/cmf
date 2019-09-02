<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Loading;

use Composer\Composer;
use LogicException;
use Modette\ModuleInstaller\Files\File;
use Modette\ModuleInstaller\Files\FileIO;
use Modette\ModuleInstaller\Package\ConfigurationValidator;
use UnexpectedValueException;

final class LoaderGenerator
{

	public function generateLoader(Composer $composer): void
	{
		if (!$this->isEnabled($composer)) {
			return;
		}

		$installationManager = $composer->getInstallationManager();
		$packages = $composer->getRepositoryManager()->getLocalRepository()->getCanonicalPackages();
		$excluded = $composer->getPackage()->getExtra()['modette']['excluded'] ?? [];

		// Filter out ignored packages and packages without modette.neon
		foreach ($packages as $key => $package) {
			// Package ignored by config
			if (in_array($package->getName(), $excluded, true)) {
				unset($packages[$key]);
			}

			// Ignore packages without modette.neon
			$packageDir = $installationManager->getInstallPath($package);

			if (!file_exists($packageDir . '/' . File::DEFAULT_NAME)) {
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
		$rootDir = $this->getRootDir($composer);
		$configFiles = [];

		foreach ($packages as $package) {
			$packageDirAbsolute = $installationManager->getInstallPath($package);
			$packageDirRelative = substr($packageDirAbsolute, strlen($rootDir));
			$configFile = $packageDirAbsolute . '/' . File::DEFAULT_NAME;
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
		$extra = $composer->getPackage()->getExtra();
		$pluginConfig = $extra['modette'] ?? [];

		if (!isset($pluginConfig['modules'])) {
			throw new LogicException('composer.json key extra.modette.modules must be defined when modette/module-installer is enabled.');
		}

		$configFileRelative = $pluginConfig['modules'];
		$configFile = $this->getRootDir($composer) . '/' . $configFileRelative;

		if (!file_exists($configFile)) {
			throw new UnexpectedValueException(sprintf(
				'composer.json key extra.modette.modules must be a valid relative path to a config file. Given path is %s, which resolved into %s',
				$configFileRelative,
				$configFile
			));
		}

		return $configFile;
	}

	private function getRootDir(Composer $composer): string
	{
		$vendorDir = $composer->getConfig()->get('vendor-dir');

		return dirname($vendorDir);
	}

	private function isEnabled(Composer $composer): bool
	{
		$extra = $composer->getPackage()->getExtra();
		$pluginConfig = $extra['modette'] ?? [];

		$enabled = $pluginConfig['enable'] ?? false;

		if (!is_bool($enabled)) {
			throw new UnexpectedValueException('composer.json key extra.modette.enable must be boolean.');
		}

		return $enabled;
	}

}