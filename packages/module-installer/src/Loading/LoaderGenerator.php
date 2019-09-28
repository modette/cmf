<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Loading;

use Composer\Composer;
use Composer\Package\PackageInterface;
use Modette\Exceptions\Logic\InvalidArgumentException;
use Modette\Exceptions\Logic\InvalidStateException;
use Modette\ModuleInstaller\Files\File;
use Modette\ModuleInstaller\Files\FileIO;
use Modette\ModuleInstaller\Package\ConfigurationValidator;
use Modette\ModuleInstaller\Package\LoaderConfiguration;
use Modette\ModuleInstaller\Package\PackageConfiguration;
use Modette\ModuleInstaller\Utils\PathResolver;
use Modette\ModuleInstaller\Utils\PluginActivator;
use Nette\PhpGenerator\ClassType;
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

	/** @var PackageConfiguration */
	private $rootPackageConfiguration;

	public function __construct(Composer $composer, PackageConfiguration $rootPackageConfiguration)
	{
		$this->composer = $composer;
		$this->io = new FileIO();
		$this->pathResolver = new PathResolver($composer);
		$this->validator = new ConfigurationValidator($composer);
		$this->rootPackageConfiguration = $rootPackageConfiguration;
	}

	public function generateLoader(): void
	{
		$loaderConfiguration = $this->rootPackageConfiguration->getLoader();

		if ($loaderConfiguration === null) {
			throw new InvalidStateException(sprintf(
				'Loader should be always available by this moment. Entry point should check if plugin is activated with \'%s\'',
				PluginActivator::class
			));
		}

		$packages = $this->composer->getRepositoryManager()->getLocalRepository()->getCanonicalPackages();
		$packages = $this->filterPackages($packages, $this->rootPackageConfiguration->getPackage(), $this->rootPackageConfiguration);

		//TODO - sort packages by priority - https://github.com/modette/modette/issues/17
		// composer show --tree
		// https://github.com/schmittjoh/composer-deps-analyzer
		// https://github.com/bulton-fr/dependency-tree
		// $packages = $this->>sortPackagesByPriority($packages);

		$packageConfigurations = [];

		foreach ($packages as $package) {
			$packageConfigurations[] = $this->validator->validateConfiguration($package, File::DEFAULT_NAME);
		}

		$packageConfigurations[] = $this->rootPackageConfiguration;

		$this->generateClass($loaderConfiguration, $packageConfigurations);
	}

	/**
	 * @param PackageInterface[] $packages
	 * @return PackageInterface[]
	 */
	private function filterPackages(array $packages, PackageInterface $rootPackage, PackageConfiguration $rootPackageConfiguration): array
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
	 * @param PackageConfiguration[] $packageConfigurations
	 */
	private function generateClass(LoaderConfiguration $loaderConfiguration, array $packageConfigurations): void
	{
		$fqn = $loaderConfiguration->getClass();
		$lastSlashPosition = strrpos($fqn, '\\');

		if ($lastSlashPosition === false) {
			throw new InvalidArgumentException('Namespace of loader class must be specified.');
		}

		$classString = substr($fqn, $lastSlashPosition + 1);
		$namespaceString = substr($fqn, 0, $lastSlashPosition);

		$file = new PhpFile();
		$file->setStrictTypes();

		$alias = $classString === 'Loader' ? 'ModuleLoader' : null;
		$namespace = $file->addNamespace($namespaceString)
			->addUse(Loader::class, $alias);

		$class = $namespace->addClass($classString)
			->setExtends(Loader::class)
			->setFinal()
			->setComment('Generated by modette/module-installer');

		$filesSchema = [];

		foreach ($packageConfigurations as $packageConfiguration) {
			$packageDirRelative = $this->pathResolver->getRelativePath($packageConfiguration->getPackage());

			foreach ($packageConfiguration->getFiles() as $fileConfiguration) {
				$filesSchema[] = [
					'file' => $packageDirRelative . '/' . $fileConfiguration->getFile(),
					'parameters' => $fileConfiguration->getParameters(),
				];
			}
		}

		$class->addProperty('files', $filesSchema)
			->setVisibility(ClassType::VISIBILITY_PROTECTED)
			->setComment('@var mixed[]');

		$loaderFilePath = $this->pathResolver->getRootDir() . '/' . $loaderConfiguration->getFile();
		$this->io->write($loaderFilePath, $file);
	}

}
