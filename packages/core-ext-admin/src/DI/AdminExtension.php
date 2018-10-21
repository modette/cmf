<?php declare(strict_types = 1);

namespace Modette\Admin\DI;

use Nette\DI\CompilerExtension;

class AdminExtension extends CompilerExtension
{

	public static function provideConfig(): string
	{
		return __DIR__ . '/config.neon';
	}

}
