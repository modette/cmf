<?php declare(strict_types = 1);

namespace App;

use Modette\Core\Boot\Configurator;
use Modette\Core\Boot\Helper\EnvironmentHelper;
use Modette\Core\Boot\Helper\HttpHelper;

class Bootstrap
{

	public static function boot(): Configurator
	{
		$configurator = new Configurator(dirname(__DIR__), new ConfigLoader());

		$configurator->setDebugMode(
			EnvironmentHelper::isEnvironmentDebugMode() ||
			HttpHelper::isLocalhost()
		);

		$configurator->addParameters(EnvironmentHelper::getEnvironmentParameters());

		return $configurator;
	}

}
