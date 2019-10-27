<?php declare(strict_types = 1);

namespace Tests\Modette\Monorepo;

use Modette\Core\Boot\Configurator;
use Modette\ModuleInstaller\Tests\PluginTestsHelper;

final class MonorepoTestsHelper
{

	/** @var bool */
	private static $generated = false;

	public static function getModuleFile(): string
	{
		return __DIR__ . '/modette.tests.neon';
	}

	public static function generateLoader(): void
	{
		if (self::$generated && class_exists(Loader::class)) {
			return;
		}

		PluginTestsHelper::generateLoader(self::getModuleFile());

		self::$generated = true;

		if (!class_exists(Loader::class)) {
			require_once __DIR__ . '/Loader.php';
		}
	}

	public static function createConfigurator(): Configurator
	{
		self::generateLoader();
		return new Configurator(dirname(__DIR__), new Loader());
	}

}
