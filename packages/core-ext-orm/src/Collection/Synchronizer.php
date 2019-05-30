<?php declare(strict_types = 1);

namespace Modette\Orm\Collection;

use Nextras\Orm\Entity\IEntity;
use Nextras\Orm\Relationships\IRelationshipCollection;

class Synchronizer
{

	/**
	 * @param mixed[] $data
	 * @param callable(mixed[] $data): IEntity $entityCreateCallback
	 * @param callable(IEntity $entity, mixed[] $data): void $entityUpdateCallback
	 * @param bool    $removeMismatchingEntities Remove entity from collection if it does not match with given data
	 */
	public function syncCollectionWithArray(
		IRelationshipCollection $entities,
		array $data,
		string $dataUniqueKeyName,
		string $entityUniqueKeyName,
		callable $entityCreateCallback,
		callable $entityUpdateCallback,
		bool $removeMismatchingEntities = false
	): void
	{
		$data = array_combine(array_column($data, $dataUniqueKeyName), $data);

		/** @var IEntity $entity */
		foreach ($entities as $entity) {
			$entityKeyValue = $entity->$entityUniqueKeyName;

			if (array_key_exists($entityKeyValue, $data)) {
				$entityUpdateCallback($entity, $data[$entityKeyValue]);
			} elseif ($removeMismatchingEntities) {
				$entities->remove($entity);
			}

			unset($data[$entityKeyValue]);
		}

		foreach ($data as $value) {
			$entities->add($entityCreateCallback($value));
		}
	}

}
