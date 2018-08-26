<?php declare(strict_types = 1);

namespace Modette\Http\DI;

use Nette\DI\CompilerExtension;

final class HttpExtension extends CompilerExtension
{

	public static function provideConfig(): string
	{
		return __DIR__ . '/config.neon';
	}

}
