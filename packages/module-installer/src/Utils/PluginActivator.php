<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Utils;

use Composer\Composer;
use UnexpectedValueException;

final class PluginActivator
{

	public static function isEnabled(Composer $composer): bool
	{
		$extra = $composer->getPackage()->getExtra();
		$pluginConfig = $extra['modette'] ?? [];

		$enabled = $pluginConfig['enable'] ?? false;

		if (!is_bool($enabled)) {
			throw new UnexpectedValueException('composer.json key extra.modette.enable must be boolean.');
		}

		return $enabled;
	}

}
