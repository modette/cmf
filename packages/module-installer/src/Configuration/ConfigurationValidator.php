<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Configuration;

use Composer\Package\PackageInterface;
use Modette\ModuleInstaller\Exception\InvalidConfigurationException;
use Modette\ModuleInstaller\Files\NeonReader;
use Modette\ModuleInstaller\Schemas\Schema;
use Modette\ModuleInstaller\Schemas\Schema_1_0;
use Modette\ModuleInstaller\Utils\PathResolver;
use Nette\Schema\Processor;
use Nette\Schema\ValidationException;

final class ConfigurationValidator
{

	/** @var NeonReader */
	private $reader;

	/** @var PathResolver */
	private $pathResolver;

	public function __construct(NeonReader $reader, PathResolver $pathResolver)
	{
		$this->reader = $reader;
		$this->pathResolver = $pathResolver;
	}

	public function validateConfiguration(PackageInterface $package, string $fileName): PackageConfiguration
	{
		$configFile = $this->pathResolver->getConfigFileFqn($package, $fileName);
		$configuration = $this->reader->read($configFile);

		if (!isset($configuration[PackageConfiguration::VERSION_OPTION])) {
			throw new InvalidConfigurationException(
				$package,
				$fileName,
				sprintf('The mandatory option \'%s\' is missing.', PackageConfiguration::VERSION_OPTION)
			);
		}

		$version = $configuration[PackageConfiguration::VERSION_OPTION];

		if (!in_array($version, Schema::VERSIONS, true)) {
			throw new InvalidConfigurationException(
				$package,
				$fileName,
				sprintf(
					'The option \'%s\' expects to be %s, %s given.',
					PackageConfiguration::VERSION_OPTION,
					implode('|', Schema::VERSIONS),
					$version
				)
			);
		}

		// First version is the only version, no need to handle $version yet
		$schema = new Schema_1_0();
		$structure = $schema->getStructure();

		$processor = new Processor();

		try {
			$configuration = $processor->process($structure, $configuration);
		} catch (ValidationException $exception) {
			throw new InvalidConfigurationException($package, $fileName, $exception->getMessage());
		}

		return new PackageConfiguration($configuration, $package);
	}

}
