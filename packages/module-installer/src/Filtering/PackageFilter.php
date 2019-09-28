<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Filtering;

use Composer\Package\PackageInterface;
use Modette\ModuleInstaller\Files\File;
use Modette\ModuleInstaller\Package\PackageConfiguration;
use Modette\ModuleInstaller\Utils\PathResolver;

final class PackageFilter
{

	/** @var PackageInterface[] */
	private $packages;

	/** @var PathResolver */
	private $pathResolver;

	/** @var PackageConfiguration */
	private $rootPackageConfiguration;

	/**
	 * @param PackageInterface[] $packages
	 */
	public function __construct(array $packages, PathResolver $pathResolver, PackageConfiguration $rootPackageConfiguration)
	{
		$this->packages = $packages;
		$this->pathResolver = $pathResolver;
		$this->rootPackageConfiguration = $rootPackageConfiguration;
	}

	/**
	 * @return PackageInterface[]
	 */
	public function getFilteredPackages(): array
	{
		//TODO - sort packages by priority - https://github.com/modette/modette/issues/17
		return $this->filterOutIncompatible($this->packages, $this->rootPackageConfiguration);
	}

	/**
	 * @param PackageInterface[] $packages
	 * @return PackageInterface[]
	 */
	private function filterOutIncompatible(array $packages, PackageConfiguration $rootPackageConfiguration): array
	{
		foreach ($packages as $key => $package) {
			// Package ignored by config
			if (in_array($package->getName(), $rootPackageConfiguration->getIgnoredPackages(), true)) {
				unset($packages[$key]);
			}

			// Ignore packages without modette.neon
			if (!file_exists($this->pathResolver->getConfigFileFqn($package, File::DEFAULT_NAME))) {
				unset($packages[$key]);
			}

			// Filter out root package, handled separately in LoaderGenerator
			if ($package === $rootPackageConfiguration->getPackage()) {
				unset($packages[$key]);
			}
		}

		return $packages;
	}

}
