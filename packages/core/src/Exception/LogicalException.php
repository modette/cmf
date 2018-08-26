<?php declare(strict_types = 1);

namespace Modette\Core\Exception;

use LogicException;
use Modette\Core\Exception\Check\UncheckedException;

class LogicalException extends LogicException implements UncheckedException
{

}
