<?php declare(strict_types = 1);

namespace Modette\Core\Logging;

use Psr\Log\LoggerInterface;

interface LoggerAccessor
{

	public function get(): LoggerInterface;

}
