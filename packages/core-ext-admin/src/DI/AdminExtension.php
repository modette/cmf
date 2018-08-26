<?php declare(strict_types = 1);

namespace Modette\Admin\DI;

class AdminExtension
{

	public static function provideConfig(): string
	{
		return __DIR__ . '/config.neon';
	}

}
