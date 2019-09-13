<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Loading;

use Composer\Composer;
use Composer\Package\PackageInterface;
use Composer\Package\RootPackageInterface;
use Exception;
use Modette\ModuleInstaller\Files\File;
use Modette\ModuleInstaller\Files\FileIO;
use Modette\ModuleInstaller\Package\ConfigurationValidator;
use Modette\ModuleInstaller\Package\LoaderConfiguration;
use Modette\ModuleInstaller\Package\PackageConfiguration;
use Modette\ModuleInstaller\Utils\PathResolver;
use Nette\PhpGenerator\PhpFile;

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
		$loaderConfiguration = $rootPackageConfiguration->getLoader();

		if ($loaderConfiguration === null) {
			throw new Exception('Should not happen - loader should be always available by this moment. Entry point should check if plugin is activated.');
		}

		$packages = $this->composer->getRepositoryManager()->getLocalRepository()->getCanonicalPackages();
		$packages = $this->filterPackages($packages, $rootPackage, $rootPackageConfiguration);

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

		$this->generateClass($loaderConfiguration, $configFiles);
	}

	/**
	 * @param PackageInterface[] $packages
	 * @return PackageInterface[]
	 */
	private function filterPackages(array $packages, RootPackageInterface $rootPackage, PackageConfiguration $rootPackageConfiguration): array
	{
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

		return $packages;
	}

	/**
	 * @param string[] $configFiles
	 */
	private function generateClass(LoaderConfiguration $loaderConfiguration, array $configFiles): void
	{
		$fqn = $loaderConfiguration->getClass();
		$lastSlashPosition = strrpos($fqn, '\\');

		if ($lastSlashPosition === false) {
			throw new Exception('Namespace of loader class must be specified.');
		}

		$classString = substr($fqn, $lastSlashPosition + 1);
		$namespaceString = substr($fqn, 0, $lastSlashPosition);

		$file = new PhpFile();
		$file->setStrictTypes();

		$alias = $classString === 'Loader' ? 'ModuleLoader' : null;
		$namespace = $file->addNamespace($namespaceString)
			->addUse(Loader::class, $alias);

		$class = $namespace->addClass($classString)
			->addImplement(Loader::class)
			->setFinal();

		$filesString = '';

		foreach ($configFiles as $configFile) {
			$filesString .= "\t'" . $configFile . "',\n";
		}

		$class->addMethod('getConfigFiles')
			->setComment('@return string[]')
			->addBody('static $files = [' . "\n" . '' . $filesString . '];')
			->addBody("\n" . 'return $files;')
			->setReturnType('array');

		$loaderFilePath = $this->pathResolver->getRootDir() . '/' . $loaderConfiguration->getFile();
		$this->io->write($loaderFilePath, $file);
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

}
