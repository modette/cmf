<?php declare(strict_types = 1);

namespace Modette\Orm\Facade;

use Modette\Exceptions\Logic\InvalidStateException;
use Nextras\Orm\Entity\IEntity;

abstract class CreateEntityFacade
{

	protected function check(IEntity $entity): void
	{
		if ($entity->isPersisted()) {
			throw new InvalidStateException(sprintf('Entity of type "%s" is already persisted. Use update facade to update entity.', get_class($entity)));
		}
	}

	abstract public function attach(IEntity $entity): void;

}
