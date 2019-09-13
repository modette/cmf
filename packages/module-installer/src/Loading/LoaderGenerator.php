<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Loading;

use Composer\Composer;
use Composer\Package\PackageInterface;
use Exception;
use Modette\ModuleInstaller\Files\File;
use Modette\ModuleInstaller\Files\FileIO;
use Modette\ModuleInstaller\Package\ConfigurationValidator;
use Modette\ModuleInstaller\Package\PackageConfiguration;
use Modette\ModuleInstaller\Utils\PathResolver;
use UnexpectedValueException;

final class LoaderGenerator
{

	/** @var Composer */
	private $composer;

	/** @var FileIO */
	private $io;

	/** @var PathResolver */
	private $pathResolver;

	/** @var ConfigurationValidator */
	private $validator;

	public function __construct(Composer $composer)
	{
		$this->composer = $composer;
		$this->io = new FileIO();
		$this->pathResolver = new PathResolver($composer);
		$this->validator = new ConfigurationValidator($composer);
	}

	public function generateLoader(): void
	{
		$rootPackage = $this->composer->getPackage();
		$rootPackageConfiguration = $this->validator->validateConfiguration($rootPackage, File::DEFAULT_NAME);
		$loaderFilePath = $this->getLoaderFilePath($rootPackageConfiguration);

		$packages = $this->composer->getRepositoryManager()->getLocalRepository()->getCanonicalPackages();

		// Filter out ignored packages and packages without modette.neon
		foreach ($packages as $key => $package) {
			// Package ignored by config
			if (in_array($package->getName(), $rootPackageConfiguration->getIgnoredPackages(), true)) {
				unset($packages[$key]);
			}

			// Ignore packages without modette.neon
			if (!file_exists($this->pathResolver->getConfigFileFqn($package))) {
				unset($packages[$key]);
			}

			// Filter out root package, handled separately
			if ($package === $rootPackage) {
				unset($packages[$key]);
			}
		}

		//TODO - sort packages by priority - https://github.com/modette/modette/issues/17
		// composer show --tree
		// https://github.com/schmittjoh/composer-deps-analyzer
		// https://github.com/bulton-fr/dependency-tree
		// $packages = $this->>sortPackagesByPriority($packages);

		$configFiles = [];

		foreach ($packages as $package) {
			$packageConfiguration = $this->validator->validateConfiguration($package, File::DEFAULT_NAME);
			$configFiles = array_merge($configFiles, $this->getConfigFiles($package, $packageConfiguration));
		}

		$configFiles = array_merge($configFiles, $this->getConfigFiles($rootPackage, $rootPackageConfiguration));

		$this->io->write($loaderFilePath, $configFiles);
	}

	/**
	 * @return string[]
	 */
	private function getConfigFiles(PackageInterface $package, PackageConfiguration $packageConfiguration): array
	{
		$packageDirRelative = $this->pathResolver->getRelativePath($package);
		$configFiles = [];

		foreach ($packageConfiguration->getFiles() as $fileConfiguration) {
			$configFiles[] = $packageDirRelative . '/' . $fileConfiguration->getFile();
		}

		return $configFiles;
	}

	private function getLoaderFilePath(PackageConfiguration $packageConfiguration): string
	{
		$loader = $packageConfiguration->getLoader();

		if ($loader === null) {
			throw new Exception('Should not happen - loader should be always available by this moment. Entry point should check if plugin is activated.');
		}

		$loaderFile = $this->pathResolver->getRootDir() . '/' . $loader->getFile();

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
