<?php declare(strict_types = 1);

namespace Modette\Api\DI;

use Nette\DI\CompilerExtension;

final class ApiExtension extends CompilerExtension
{

	public static function provideConfig(): string
	{
		return __DIR__ . '/config.neon';
	}

}
