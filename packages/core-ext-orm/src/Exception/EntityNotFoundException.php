<?php declare(strict_types = 1);

namespace Modette\Orm\Exception;

use Modette\Core\Exception\Check\CheckedException;
use RuntimeException;

class EntityNotFoundException extends RuntimeException implements CheckedException
{

}
