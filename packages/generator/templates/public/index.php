<?php declare(strict_types = 1);

use Nette\Application\Application;
use Nette\DI\Container;

/** @var Container $container */
$container = require __DIR__ . '/../src/bootstrap.php';

$container->getByType(Application::class)->run();
