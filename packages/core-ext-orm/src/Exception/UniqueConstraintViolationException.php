<?php declare(strict_types = 1);

namespace Modette\Orm\Exception;

use Modette\Core\Exception\Check\CheckedException;
use RuntimeException;

class UniqueConstraintViolationException extends RuntimeException implements CheckedException
{

}
