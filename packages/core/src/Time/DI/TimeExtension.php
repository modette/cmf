<?php declare(strict_types = 1);

namespace Modette\Core\Time\DI;

use Nette\DI\CompilerExtension;
use Nette\PhpGenerator\ClassType;

final class TimeExtension extends CompilerExtension
{

	private const INI_OPTION = 'date.timezone';
	private const TIMEZONE = 'UTC';

	public function loadConfiguration(): void
	{
		date_default_timezone_set(self::TIMEZONE);
		ini_set(self::INI_OPTION, self::TIMEZONE);
	}

	public function afterCompile(ClassType $class): void
	{
		$initialize = $class->getMethod('initialize');

		$initialize->addBody('date_default_timezone_set(?);', [self::TIMEZONE]);
		$initialize->addBody('ini_set(?, ?);', [self::INI_OPTION, self::TIMEZONE]);
	}

}
