<?php declare(strict_types = 1);

namespace Modette\Orm\Exception;

use Modette\Exceptions\Check\CheckedException;
use RuntimeException;

class UniqueConstraintViolationException extends RuntimeException implements CheckedException
{

}
