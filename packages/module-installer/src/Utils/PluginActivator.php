<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Utils;

use Composer\Composer;
use Modette\Exceptions\Logic\InvalidStateException;
use Modette\ModuleInstaller\Package\ConfigurationValidator;
use Modette\ModuleInstaller\Package\PackageConfiguration;

final class PluginActivator
{

	/** @var Composer */
	private $composer;

	/** @var string */
	private $fileName;

	/** @var PackageConfiguration|null */
	private $configuration;

	/** @var string|null */
	private $configFileFqn;

	public function __construct(Composer $composer, string $fileName)
	{
		$this->composer = $composer;
		$this->fileName = $fileName;
	}

	public function isEnabled(): bool
	{
		if (!file_exists($this->getConfigFileFqn())) {
			return false;
		}

		return $this->getConfiguration()->getLoader() !== null;
	}

	public function getConfiguration(): PackageConfiguration
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

		$validator = new ConfigurationValidator($this->composer);
		$configuration = $this->configuration = $validator->validateConfiguration($this->composer->getPackage(), $this->fileName);

		return $configuration;
	}

	private function getConfigFileFqn(): string
	{
		if ($this->configFileFqn !== null) {
			return $this->configFileFqn;
		}

		$pathResolver = new PathResolver($this->composer);
		$rootPackage = $this->composer->getPackage();
		$configFile = $this->configFileFqn = $pathResolver->getConfigFileFqn($rootPackage);

		return $configFile;
	}

}
