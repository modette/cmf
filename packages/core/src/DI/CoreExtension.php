<?php declare(strict_types = 1);

namespace Modette\Core\DI;

use Nette\DI\CompilerExtension;

final class CoreExtension extends CompilerExtension
{

	public static function provideConfig(): string
	{
		return __DIR__ . '/config.neon';
	}

}
