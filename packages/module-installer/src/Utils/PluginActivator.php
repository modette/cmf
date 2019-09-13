<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Utils;

use Composer\Composer;
use Modette\ModuleInstaller\Files\File;
use Modette\ModuleInstaller\Files\FileIO;
use Modette\ModuleInstaller\Package\ConfigurationValidator;

final class PluginActivator
{

	public static function isEnabled(Composer $composer): bool
	{
		$pathResolver = new PathResolver($composer);
		$rootPackage = $composer->getPackage();
		$configFile = $pathResolver->getConfigFileFqn($rootPackage);

		if (!file_exists($configFile)) {
			return false;
		}

		$validator = new ConfigurationValidator();
		$io = new FileIO();
		$configuration = $validator->validateConfiguration($rootPackage->getName(), File::DEFAULT_NAME, $io->read($configFile));

		return $configuration->getLoader() !== null;
	}

}
