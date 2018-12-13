<?php declare(strict_types = 1);

namespace App;

use Modette\Core\Boot\Configurator;
use Modette\Core\Boot\Helper\EnvironmentHelper;
use Modette\Core\Boot\Helper\HttpHelper;

$rootDir = dirname(__DIR__);
require $rootDir . '/vendor/autoload.php';
$configurator = new Configurator($rootDir);

$configurator->setDebugMode(
	EnvironmentHelper::isEnvironmentDebugMode() ||
	$configurator->isConsoleMode() ||
	HttpHelper::isLocalhost()
);

$configurator->setModulesConfig(__DIR__ . '/../config/modules.neon');

$configurator->addConfig(__DIR__ . '/../config/config.neon');

return $configurator->createContainer();
