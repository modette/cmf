<?php declare(strict_types = 1);

namespace Modette\Mail\DI;

use Nette\DI\CompilerExtension;

final class MailExtension extends CompilerExtension
{

	public static function provideConfig(): string
	{
		return __DIR__ . '/config.neon';
	}

}
