<?php declare(strict_types = 1);

namespace Tests\Modette\Http;

use Modette\Core\Boot\Configurator;
use Modette\ModuleInstaller\Tests\PluginTestsHelper;

final class HttpTestsHelper
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
		$pathParts = explode(DIRECTORY_SEPARATOR, __DIR__);
		$levels = $pathParts[count($pathParts) - 3] === 'packages' ? 3 : 1;
		return new Configurator(dirname(__DIR__, $levels), new Loader());
	}

}
