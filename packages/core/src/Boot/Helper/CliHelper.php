<?php declare(strict_types = 1);

namespace Modette\Core\Boot\Helper;

class CliHelper
{

	public static function isCli(): bool
	{
		return PHP_SAPI === 'cli';
	}

}
