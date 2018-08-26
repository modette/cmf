<?php declare(strict_types = 1);

namespace App;

use Modette\Core\Configurator;

$rootDir = dirname(__DIR__);
require $rootDir . '/vendor/autoload.php';
$configurator = new Configurator($rootDir);

$configurator->setDebugMode(
	$configurator->isEnvironmentDebugMode() ||
	$configurator->isConsoleMode() ||
	$configurator->isLocalhost()
);

$configurator->loadModules(__DIR__ . '/../config/modules.neon');

$configurator->addConfig(__DIR__ . '/../config/config.neon');

return $configurator->createContainer();
