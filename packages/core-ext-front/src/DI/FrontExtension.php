<?php declare(strict_types = 1);

namespace Modette\Front\DI;

use Nette\DI\CompilerExtension;

final class FrontExtension extends CompilerExtension
{

	public static function provideConfig(): string
	{
		return __DIR__ . '/config.neon';
	}

}
