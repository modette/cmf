<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Resolving;

use Composer\Json\JsonFile;
use Composer\Package\Link;
use Composer\Package\Loader\ArrayLoader;
use Composer\Package\Loader\LoaderInterface;
use Composer\Package\Loader\ValidatingArrayLoader;
use Composer\Package\PackageInterface;
use Composer\Repository\WritableRepositoryInterface;
use Composer\Semver\Constraint\EmptyConstraint;
use Modette\Exceptions\Logic\InvalidArgumentException;
use Modette\ModuleInstaller\Configuration\ConfigurationValidator;
use Modette\ModuleInstaller\Configuration\PackageConfiguration;
use Modette\ModuleInstaller\Configuration\SimulatedModuleConfiguration;
use Modette\ModuleInstaller\Monorepo\SimulatedPackage;
use Modette\ModuleInstaller\Plugin;
use Modette\ModuleInstaller\Utils\PathResolver;

final class ModuleResolver
{

	/** @var WritableRepositoryInterface */
	private $repository;

	/** @var PathResolver */
	private $pathResolver;

	/** @var ConfigurationValidator */
	private $validator;

	/** @var PackageConfiguration */
	private $rootPackageConfiguration;

	public function __construct(WritableRepositoryInterface $repository, PathResolver $pathResolver, ConfigurationValidator $validator, PackageConfiguration $rootPackageConfiguration)
	{
		$this->repository = $repository;
		$this->pathResolver = $pathResolver;
		$this->validator = $validator;
		$this->rootPackageConfiguration = $rootPackageConfiguration;
	}

	/**
	 * @return PackageConfiguration[]
	 */
	public function getResolvedConfigurations(): array
	{
		$packageLoader = new ValidatingArrayLoader(new ArrayLoader());

		/** @var PackageInterface[] $packages */
		$packages = $this->repository->getCanonicalPackages();
		$packages[] = $this->rootPackageConfiguration->getPackage();

		$modules = $this->getModulesFromPackages($packages, $packageLoader);

		/** @var string[] $ignoredByModule */
		$ignoredByModule = [];

		foreach ($modules as $module) {
			$ignoredByModule[] = $module->getConfiguration()->getIgnoredPackages();

			$module->setDependents(
				$this->packagesToModules(
					$this->flatten($this->getDependents($module->getConfiguration()->getPackage()->getName(), $packages)),
					$modules
				)
			);
		}

		/** @var string[] $ignored */
		$ignored = array_merge(...$ignoredByModule);

		// TODO
		//		modules are not sorted properly when simulated packages are used
		//		main package uses all deps of simulated packages - it may cause issues?
		//		probably problem with uasort - it should be used only to compare simple values, while modules have dependencies
		//		each module have own dependencies and uasort don't count with it
		uasort($modules, static function (Module $m1, Module $m2) {
			$d1 = $m1->getDependents();
			$n1 = $m1->getConfiguration()->getPackage()->getName();

			$d2 = $m2->getDependents();
			$n2 = $m2->getConfiguration()->getPackage()->getName();

			// Cyclical dependency, ignore
			if (isset($d1[$n2], $d2[$n1])) {
				return 0;
			}

			if (isset($d1[$n2])) {
				return -1;
			}

			return 1;
		});

		$packageConfigurations = [];

		foreach ($modules as $module) {
			// Skip package configuration if listed in ignored
			if (in_array($module->getConfiguration()->getPackage()->getName(), $ignored, true)) {
				continue;
			}

			$packageConfigurations[] = $module->getConfiguration();
		}

		return $packageConfigurations;
	}

	/**
	 * @param PackageInterface[] $packages
	 * @return Module[]
	 */
	private function getModulesFromPackages(array $packages, LoaderInterface $packageLoader): array
	{
		$modules = [];
		$modulesBySubpackage = [];
		$rootPackage = $this->rootPackageConfiguration->getPackage();

		foreach ($packages as $package) {
			if (!$this->isApplicable($package)) {
				if ($package instanceof SimulatedPackage) {
					throw new InvalidArgumentException(sprintf(
						'Cannot set "%s" as a "%s" simulated module. Given package does not have "%s" file.',
						$package->getName(),
						$package->getParentName(),
						Plugin::DEFAULT_FILE_NAME
					));
				}

				continue;
			}

			$packageConfiguration = $package === $rootPackage
				? $this->rootPackageConfiguration
				: $this->validator->validateConfiguration($package, Plugin::DEFAULT_FILE_NAME);

			$modulesBySubpackage[] = $this->getModulesFromPackages($this->getSimulatedPackages($packageConfiguration, $packageLoader), $packageLoader);

			$modules[$package->getName()] = new Module($packageConfiguration);
		}

		return array_merge($modules, ...$modulesBySubpackage);
	}

	/**
	 * Returns list of explicitly allowed packages, which are part of monorepo like they were really installed
	 *
	 * @return SimulatedPackage[]
	 */
	private function getSimulatedPackages(PackageConfiguration $packageConfiguration, LoaderInterface $packageLoader): array
	{
		$parentPackage = $packageConfiguration->getPackage();
		$parentPackageName = $parentPackage->getName();
		$parentFullPath = $this->pathResolver->getAbsolutePath($parentPackage);
		$schemaPath = $packageConfiguration->getSchemaPath();

		$packages = [];

		foreach ($packageConfiguration->getSimulatedModules() as $module) {
			$name = $module->getName();

			// Package exists, simulation not needed
			if ($this->repository->findPackage($name, new EmptyConstraint()) !== null) {
				continue;
			}

			$path = $module->getPath();

			$directoryPath = $this->pathResolver->buildPathFromParts([
				$parentFullPath,
				$schemaPath,
				$path,
			]);
			$composerFilePath = $this->pathResolver->buildPathFromParts([
				$directoryPath,
				'composer.json',
			]);

			if (!file_exists($composerFilePath)) {
				if ($module->isOptional()) {
					continue;
				}

				throw new InvalidArgumentException(sprintf(
					'Package "%s" is not installed and simulated module path "%s" is invalid, "%s" not found. If it is an optional dependency then set %s: true',
					$name,
					$path,
					$composerFilePath,
					SimulatedModuleConfiguration::OPTIONAL_OPTION
				));
			}

			$config = JsonFile::parseJson(file_get_contents($composerFilePath), $composerFilePath) + ['version' => '999.999.999'];

			$package = $packageLoader->load($config, SimulatedPackage::class);
			assert($package instanceof SimulatedPackage);
			$packageName = $package->getName();

			if ($name !== $packageName) {
				throw new InvalidArgumentException(sprintf(
					'Path "%s" contains package "%s" while package "%s" was expected by configuration.',
					$path,
					$packageName,
					$name
				));
			}

			$package->setParentName($parentPackageName);
			$package->setPackageDirectory($directoryPath);
			$packages[] = $package;
		}

		return $packages;
	}

	/**
	 * Filter out packages with no modette.neon (root package is always applicable)
	 */
	private function isApplicable(PackageInterface $package): bool
	{
		static $cache = [];
		$name = $package->getName();
		return $cache[$name]
			?? $cache[$name] = ($package === $this->rootPackageConfiguration->getPackage()
					|| file_exists($this->pathResolver->getSchemaFileFullName($package, Plugin::DEFAULT_FILE_NAME)));
	}

	private function getPackageFromLink(Link $link): ?PackageInterface
	{
		static $cache = [];
		$name = $link->getTarget();
		return $cache[$name] ?? $cache[$name] = $this->repository->findPackage($name, new EmptyConstraint());
	}

	/**
	 * @param PackageInterface[] $packages
	 * @param Module[]           $modules
	 * @return Module[]
	 */
	private function packagesToModules(array $packages, array $modules): array
	{
		$result = [];

		foreach ($packages as $package) {
			$name = $package->getName();
			if (isset($modules[$name])) {
				$result[$name] = $modules[$name];
			}
		}

		return $result;
	}

	/**
	 * @param mixed[] $dependents
	 * @return PackageInterface[]
	 */
	private function flatten(array $dependents): array
	{
		$deps = [];

		foreach ($dependents as $dependent) {
			[$package, $children] = $dependent;
			assert($package instanceof PackageInterface);
			assert(is_array($children) || $children === null);

			$name = $package->getName();

			if (!isset($deps[$name])) {
				$deps[$name] = $package;
			}

			if ($children !== null) {
				$deps += $this->flatten($children);
			}
		}

		return $deps;
	}

	/**
	 * Returns a list of packages causing the requested needle packages to be installed.
	 *
	 * @param string             $needle The package name to inspect.
	 * @param PackageInterface[] $packages
	 * @param string[]|null      $packagesFound Used internally when recurring
	 * @return mixed[][] ['packageName' => [$package, $dependents|null]]
	 */
	private function getDependents(string $needle, array $packages, ?array $packagesFound = null): array
	{
		$needle = strtolower($needle);
		$results = [];

		// Initialize the array with the needles before any recursion occurs
		if ($packagesFound === null) {
			$packagesFound = [$needle];
		}

		// Loop over all currently installed packages.
		foreach ($packages as $package) {
			// Skip non-module packages
			if (!$this->isApplicable($package)) {
				continue;
			}

			$links = $package->getRequires();

			// Each loop needs its own "tree" as we want to show the complete dependent set of every needle
			// without warning all the time about finding circular deps
			$packagesInTree = $packagesFound;

			// Replaces are relevant for order
			$links += $package->getReplaces();

			// Only direct dev-requires are relevant and only if they represent modules
			$devLinks = $package->getDevRequires();

			foreach ($devLinks as $key => $link) {
				$resolvedDevPackage = $this->getPackageFromLink($link);

				if ($resolvedDevPackage === null || !$this->isApplicable($resolvedDevPackage)) {
					unset($devLinks[$key]);
				}
			}

			$links += $devLinks;

			// Cross-reference all discovered links to the needles
			foreach ($links as $link) {
				if ($link->getTarget() === $needle) {
					// already resolved this node's dependencies
					if (in_array($link->getSource(), $packagesInTree, true)) {
						$results[$link->getSource()] = [$package, null];
						continue;
					}

					$packagesInTree[] = $link->getSource();
					$dependents = $this->getDependents($link->getSource(), $packages, $packagesInTree);
					$results[$link->getSource()] = [$package, $dependents];
				}
			}
		}

		ksort($results);

		return $results;
	}

}
