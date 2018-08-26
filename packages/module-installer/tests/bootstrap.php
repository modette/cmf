<?php declare(strict_types = 1);

use Ninjify\Nunjuck\Environment;

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
	require_once __DIR__ . '/../vendor/autoload.php';
} elseif (file_exists(__DIR__ . '/../../../vendor/autoload.php')) {
	require_once __DIR__ . '/../../../vendor/autoload.php';
} else {
	echo 'Install ninjify/nunjuck using `composer install`';
	exit(1);
}

Environment::setup(__DIR__);
