<?php declare(strict_types = 1);

namespace Modette\Core\Boot\Helper;

class CliHelper
{

	public static function isCli(): bool
	{
		return PHP_SAPI === 'cli' && php_sapi_name() !== 'cli-server'; // 'cli-server' is php built-in webserver for which is PHP_SAPI equal to 'cli'
	}

}
