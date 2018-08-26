<?php declare(strict_types = 1);

namespace Modette\Orm\Listeners;

use Contributte\Nextras\Orm\Events\Listeners\BeforeUpdateListener;
use DateTimeImmutable;
use Nextras\Orm\Entity\IEntity;

class UpdatedAtListener implements BeforeUpdateListener
{

	public function onBeforeUpdate(IEntity $entity): void
	{
		$entity->setReadOnlyValue('updatedAt', new DateTimeImmutable('now'));
	}

}
