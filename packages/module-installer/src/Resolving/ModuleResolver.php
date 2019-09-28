<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Resolving;

use Composer\Composer;
use Composer\Package\PackageInterface;
use Modette\ModuleInstaller\Files\File;
use Modette\ModuleInstaller\Package\ConfigurationValidator;
use Modette\ModuleInstaller\Package\PackageConfiguration;
use Modette\ModuleInstaller\Utils\PathResolver;

final class ModuleResolver
{

	/** @var Composer */
	private $composer;

	/** @var PathResolver */
	private $pathResolver;

	/** @var ConfigurationValidator */
	private $validator;

	/** @var PackageConfiguration */
	private $rootPackageConfiguration;

	public function __construct(Composer $composer, PathResolver $pathResolver, ConfigurationValidator $validator, PackageConfiguration $rootPackageConfiguration)
	{
		$this->composer = $composer;
		$this->pathResolver = $pathResolver;
		$this->validator = $validator;
		$this->rootPackageConfiguration = $rootPackageConfiguration;
	}

	/**
	 * @return PackageConfiguration[]
	 */
	public function getResolvedConfigurations(): array
	{
		$packages = $this->composer->getRepositoryManager()->getLocalRepository()->getCanonicalPackages();

		$packageConfigurations = [];

		foreach ($packages as $package) {
			if (!$this->isApplicable($package)) {
				continue;
			}

			//TODO - sort packages by priority - https://github.com/modette/modette/issues/17

			$packageConfigurations[] = $this->validator->validateConfiguration($package, File::DEFAULT_NAME);
		}

		$packageConfigurations[] = $this->rootPackageConfiguration;

		return $packageConfigurations;
	}

	/**
	 * Filter out packages ignored by config, with no modette.neon and root package (which is handled separately)
	 */
	private function isApplicable(PackageInterface $package): bool
	{
		return !in_array($package->getName(), $this->rootPackageConfiguration->getIgnoredPackages(), true)
			&& file_exists($this->pathResolver->getConfigFileFqn($package, File::DEFAULT_NAME))
			&& $package !== $this->rootPackageConfiguration->getPackage();
	}

}
