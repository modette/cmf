<?php declare(strict_types = 1);

namespace Modette\Orm\Facade;

use Modette\Exceptions\Logic\InvalidStateException;
use Nextras\Orm\Entity\IEntity;

abstract class UpdateEntityFacade
{

	protected function check(IEntity $entity): void
	{
		if (!$entity->isPersisted()) {
			throw new InvalidStateException(sprintf('Entity of type "%s" is not persisted yet. Use create facade to create entity.', get_class($entity)));
		}
	}

}
