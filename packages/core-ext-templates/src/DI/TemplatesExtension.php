<?php declare(strict_types = 1);

namespace Modette\Templates\DI;

use Nette\DI\CompilerExtension;

final class TemplatesExtension extends CompilerExtension
{

	public static function provideConfig(): string
	{
		return __DIR__ . '/config.neon';
	}

}
