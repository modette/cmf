<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Package;

use Modette\ModuleInstaller\Exception\InvalidConfigurationException;
use Modette\ModuleInstaller\Schemas\Schema;
use Modette\ModuleInstaller\Schemas\Schema_1_0;
use Nette\Schema\Processor;
use Nette\Schema\ValidationException;

final class ConfigurationValidator
{

	/**
	 * @param mixed[] $configuration
	 */
	public function validateConfiguration(string $package, string $fileName, array $configuration): RootConfiguration
	{
		if (!isset($configuration['version'])) {
			throw new InvalidConfigurationException($package, $fileName, 'The mandatory option \'version\' is missing.');
		}

		$version = $configuration['version'];

		if (!in_array($version, Schema::VERSIONS, true)) {
			throw new InvalidConfigurationException(
				$package,
				$fileName,
				sprintf('The option \'version\' expects to be %s, %s given.', implode('|', Schema::VERSIONS), $version)
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

		return new RootConfiguration($configuration);
	}

}
