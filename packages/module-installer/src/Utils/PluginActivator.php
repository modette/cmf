<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Utils;

use Composer\Composer;
use Modette\ModuleInstaller\Files\File;
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

		$validator = new ConfigurationValidator($composer);
		$packageConfiguration = $validator->validateConfiguration($rootPackage, File::DEFAULT_NAME);

		return $packageConfiguration->getLoader() !== null;
	}

}
