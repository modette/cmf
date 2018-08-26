<?php declare(strict_types = 1);

namespace Modette\Sql\DI;

use Nette\DI\CompilerExtension;

final class SqlExtension extends CompilerExtension
{

	public static function provideConfig(): string
	{
		return __DIR__ . '/config.neon';
	}

}
