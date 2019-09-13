<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Loading;

use Composer\Composer;
use Composer\Package\RootPackageInterface;
use Exception;
use Modette\ModuleInstaller\Files\File;
use Modette\ModuleInstaller\Files\FileIO;
use Modette\ModuleInstaller\Package\ConfigurationValidator;
use Modette\ModuleInstaller\Utils\PathResolver;
use UnexpectedValueException;

final class LoaderGenerator
{

	/** @var FileIO */
	private $io;

	/** @var ConfigurationValidator */
	private $validator;

	public function __construct()
	{
		$this->io = new FileIO();
		$this->validator = new ConfigurationValidator();
	}

	public function generateLoader(Composer $composer): void
	{
		$pathResolver = new PathResolver($composer);
		$packages = $composer->getRepositoryManager()->getLocalRepository()->getCanonicalPackages();
		$rootPackage = $composer->getPackage();
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

			// Filter out root package, added as last
			if ($package === $rootPackage) {
				unset($packages[$key]);
			}
		}

		$packages[] = $rootPackage;

		//TODO - sort packages by priority - https://github.com/modette/modette/issues/17
		// composer show --tree
		// https://github.com/schmittjoh/composer-deps-analyzer
		// https://github.com/bulton-fr/dependency-tree
		// $packages = $this->>sortPackagesByPriority($packages);

		$configFiles = [];

		foreach ($packages as $package) {
			$packageDirRelative = $pathResolver->getRelativePath($package);
			$configFile = $pathResolver->getConfigFileFqn($package);
			$configuration = $this->validator->validateConfiguration($package->getName(), File::DEFAULT_NAME, $this->io->read($configFile));

			foreach ($configuration->getFiles() as $fileConfiguration) {
				$configFiles[] = $packageDirRelative . '/' . $fileConfiguration->getFile();
			}
		}

		$this->io->write($this->getLoaderFilePath($composer, $rootPackage), $configFiles);
	}

	private function getLoaderFilePath(Composer $composer, RootPackageInterface $rootPackage): string
	{
		$pathResolver = new PathResolver($composer);
		$configFile = $pathResolver->getConfigFileFqn($rootPackage);
		$configuration = $this->validator->validateConfiguration($rootPackage->getName(), File::DEFAULT_NAME, $this->io->read($configFile));

		$loader = $configuration->getLoader();

		if ($loader === null) {
			throw new Exception('Should not happen - loader should be always available by this moment. Entry point should check if plugin is activated.');
		}

		$loaderFile = $pathResolver->getRootDir() . '/' . $loader->getFile();

		if (!file_exists($loaderFile)) {
			throw new UnexpectedValueException(sprintf(
				'%s key loader.file must be a valid relative path to a config file. Given path is %s, which resolved into %s',
				File::DEFAULT_NAME,
				$loader->getFile(),
				$loaderFile
			));
		}

		return $loaderFile;
	}

}
