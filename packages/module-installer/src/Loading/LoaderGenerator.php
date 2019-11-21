<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Loading;

use Composer\Repository\WritableRepositoryInterface;
use Composer\Semver\Constraint\EmptyConstraint;
use Modette\Exceptions\Logic\InvalidArgumentException;
use Modette\Exceptions\Logic\InvalidStateException;
use Modette\ModuleInstaller\Configuration\ConfigurationValidator;
use Modette\ModuleInstaller\Configuration\FileConfiguration;
use Modette\ModuleInstaller\Configuration\LoaderConfiguration;
use Modette\ModuleInstaller\Configuration\PackageConfiguration;
use Modette\ModuleInstaller\Files\Writer;
use Modette\ModuleInstaller\Resolving\ModuleResolver;
use Modette\ModuleInstaller\Utils\PathResolver;
use Modette\ModuleInstaller\Utils\PluginActivator;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpFile;

final class LoaderGenerator
{

	/** @var WritableRepositoryInterface */
	private $repository;

	/** @var Writer */
	private $writer;

	/** @var PathResolver */
	private $pathResolver;

	/** @var ConfigurationValidator */
	private $validator;

	/** @var PackageConfiguration */
	private $rootPackageConfiguration;

	public function __construct(WritableRepositoryInterface $repository, Writer $writer, PathResolver $pathResolver, ConfigurationValidator $validator, PackageConfiguration $rootPackageConfiguration)
	{
		$this->repository = $repository;
		$this->writer = $writer;
		$this->pathResolver = $pathResolver;
		$this->validator = $validator;
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

		$resolver = new ModuleResolver(
			$this->repository,
			$this->pathResolver,
			$this->validator,
			$this->rootPackageConfiguration
		);

		$this->generateClass($loaderConfiguration, $resolver->getResolvedConfigurations());
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

		$itemsByPriority = [
			FileConfiguration::PRIORITY_VALUE_HIGH => [],
			FileConfiguration::PRIORITY_VALUE_NORMAL => [],
			FileConfiguration::PRIORITY_VALUE_LOW => [],
		];

		$modulesMeta = [];

		$switchesByPackage = [];

		foreach ($packageConfigurations as $packageConfiguration) {
			$switchesByPackage[] = $packageConfiguration->getSwitches();
		}

		$switches = array_merge(...$switchesByPackage);

		foreach ($packageConfigurations as $packageConfiguration) {
			$package = $packageConfiguration->getPackage();
			$packageName = $package->getName();
			$packageDirRelative = $this->pathResolver->getRelativePath($package);

			if ($packageName !== '__root__') {
				$modulesMeta[$package->getName()] = [
					BaseLoader::META_ITEM_DIR => $packageDirRelative,
				];
			}

			foreach ($packageConfiguration->getFiles() as $fileConfiguration) {
				// Skip configuration if required package is not installed
				foreach ($fileConfiguration->getRequiredPackages() as $requiredPackage) {
					if ($this->repository->findPackage($requiredPackage, new EmptyConstraint()) === null) {
						continue 2;
					}
				}

				$item = [
					BaseLoader::SCHEMA_ITEM_FILE => $this->pathResolver->buildPathFromParts([
						$packageDirRelative,
						$packageConfiguration->getSchemaPath(),
						$fileConfiguration->getFile(),
					]),
				];

				$itemSwitches = $fileConfiguration->getSwitches();

				foreach ($itemSwitches as $itemSwitchName => $itemSwitchValue) {
					if (!isset($switches[$itemSwitchName])) {
						throw new InvalidArgumentException(sprintf(
							'Configuration file switch \'%s\' is not defined in \'%s\'.',
							$itemSwitchName,
							PackageConfiguration::SWITCHES_OPTION
						));
					}
				}

				if ($itemSwitches !== []) {
					$item[BaseLoader::SCHEMA_ITEM_SWITCHES] = $itemSwitches;
				}

				$itemsByPriority[$fileConfiguration->getPriority()][] = $item;
			}
		}

		$schema = array_merge(
			$itemsByPriority[FileConfiguration::PRIORITY_VALUE_HIGH],
			$itemsByPriority[FileConfiguration::PRIORITY_VALUE_NORMAL],
			$itemsByPriority[FileConfiguration::PRIORITY_VALUE_LOW]
		);

		if (class_exists($fqn)) {
			if (!is_subclass_of($fqn, BaseLoader::class)) {
				throw new InvalidStateException(sprintf(
					'\'%s\' should be instance of \'%s\'',
					$fqn,
					BaseLoader::class
				));
			}

			$loader = new $fqn();
			assert($loader instanceof BaseLoader);

			if ($loader->getSchema() === $schema && $loader->getModulesMeta() === $modulesMeta && $loader->getSwitches() === $switches) {
				return;
			}
		}

		$classString = substr($fqn, $lastSlashPosition + 1);
		$namespaceString = substr($fqn, 0, $lastSlashPosition);

		$file = new PhpFile();
		$file->setStrictTypes();

		$alias = $classString === substr(strrchr(BaseLoader::class, '\\'), 1) ? 'Loader' : null;
		$namespace = $file->addNamespace($namespaceString)
			->addUse(BaseLoader::class, $alias);

		$class = $namespace->addClass($classString)
			->setExtends(BaseLoader::class)
			->setFinal()
			->setComment('Generated by modette/module-installer');

		$class->addProperty('schema', $schema)
			->setVisibility(ClassType::VISIBILITY_PROTECTED)
			->setComment('@var mixed[]');

		$class->addProperty('switches', $switches)
			->setVisibility(ClassType::VISIBILITY_PROTECTED)
			->setComment('@var bool[]');

		$class->addProperty('modulesMeta', $modulesMeta)
			->setVisibility(ClassType::VISIBILITY_PROTECTED)
			->setComment('@var mixed[]');

		$loaderFilePath = $this->pathResolver->buildPathFromParts([
			$this->pathResolver->getRootDir(),
			$this->rootPackageConfiguration->getSchemaPath(),
			$loaderConfiguration->getFile(),
		]);
		$this->writer->write($loaderFilePath, $file);
	}

}
