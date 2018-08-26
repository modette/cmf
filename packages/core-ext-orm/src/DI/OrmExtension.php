<?php declare(strict_types = 1);

namespace Modette\Orm\DI;

use Nette\DI\CompilerExtension;

final class OrmExtension extends CompilerExtension
{

	public static function provideConfig(): string
	{
		return __DIR__ . '/config.neon';
	}

}
