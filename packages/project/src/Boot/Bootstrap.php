<?php declare(strict_types = 1);

namespace App\Boot;

use Modette\Core\Boot\Configurator;
use Modette\Core\Boot\Helper\EnvironmentHelper;
use Modette\Core\Boot\Helper\HttpHelper;

final class Bootstrap
{

	public static function boot(): Configurator
	{
		$configurator = new Configurator(dirname(__DIR__, 2), new ConfigLoader());

		$configurator->setDebugMode(
			EnvironmentHelper::isEnvironmentDebugMode() ||
			HttpHelper::isLocalhost()
		);
		$configurator->addParameters(EnvironmentHelper::getEnvironmentParameters());
		$configurator->enableDebugger();

		return $configurator;
	}

}
