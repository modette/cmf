<?php declare(strict_types = 1);

namespace Modette\Orm\Exception;

use Modette\Exceptions\Check\CheckedException;
use RuntimeException;

class EntityNotFoundException extends RuntimeException implements CheckedException
{

}
