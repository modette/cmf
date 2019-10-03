<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Utils;

use Composer\Package\PackageInterface;
use Modette\Exceptions\Logic\InvalidStateException;
use Modette\ModuleInstaller\Configuration\ConfigurationValidator;
use Modette\ModuleInstaller\Configuration\PackageConfiguration;

final class PluginActivator
{

	/** @var PackageInterface */
	private $rootPackage;

	/** @var ConfigurationValidator */
	private $validator;

	/** @var PathResolver */
	private $pathResolver;

	/** @var string */
	private $fileName;

	/** @var PackageConfiguration|null */
	private $configuration;

	/** @var string|null */
	private $configFileFqn;

	public function __construct(PackageInterface $rootPackage, ConfigurationValidator $validator, PathResolver $pathResolver, string $fileName)
	{
		$this->rootPackage = $rootPackage;
		$this->validator = $validator;
		$this->pathResolver = $pathResolver;
		$this->fileName = $fileName;
	}

	public function isEnabled(): bool
	{
		if (!file_exists($this->getConfigFileFqn())) {
			return false;
		}

		return $this->getRootPackageConfiguration()->getLoader() !== null;
	}

	public function getRootPackageConfiguration(): PackageConfiguration
	{
		if ($this->configuration !== null) {
			return $this->configuration;
		}

		if (!file_exists($this->getConfigFileFqn())) {
			throw new InvalidStateException(sprintf(
				'Plugin is not activated, check with \'%s()\' before calling \'%s\'',
				self::class . '::isEnabled()',
				self::class . '::' . __METHOD__ . '()'
			));
		}

		$configuration = $this->configuration = $this->validator->validateConfiguration($this->rootPackage, $this->fileName);

		return $configuration;
	}

	private function getConfigFileFqn(): string
	{
		if ($this->configFileFqn !== null) {
			return $this->configFileFqn;
		}

		$configFile = $this->configFileFqn = $this->pathResolver->getConfigFileFqn($this->rootPackage, $this->fileName);

		return $configFile;
	}

}
