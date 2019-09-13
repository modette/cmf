<?php declare(strict_types = 1);

namespace Boot;

use Modette\Core\Boot\Configurator;
use Modette\Core\Boot\Helper\EnvironmentHelper;
use Modette\Core\Boot\Helper\HttpHelper;

class Bootstrap
{

	public static function boot(): Configurator
	{
		$configurator = new Configurator(dirname(__DIR__));

		$configurator->setDebugMode(
			EnvironmentHelper::isEnvironmentDebugMode() ||
			HttpHelper::isLocalhost()
		);

		$configurator->setLoader(new ConfigLoader());
		$configurator->addParameters(EnvironmentHelper::getEnvironmentParameters());

		$configurator->addConfig(__DIR__ . '/../config/base.neon');
		$configurator->addConfig(__DIR__ . '/../config/server/local.neon');

		return $configurator;
	}

}
