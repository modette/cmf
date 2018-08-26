<?php declare(strict_types = 1);

namespace Modette\UI\DI;

use Nette\DI\CompilerExtension;

final class UIExtension extends CompilerExtension
{

	public static function provideConfig(): string
	{
		return __DIR__ . '/config.neon';
	}

}
