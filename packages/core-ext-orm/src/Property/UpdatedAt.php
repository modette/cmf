<?php declare(strict_types = 1);

namespace Modette\Orm\Property;

use Nextras\Dbal\Utils\DateTimeImmutable;

/**
 * @BeforePersist(Modette\Orm\Listener\UpdatedAtListener)
 * @property-read DateTimeImmutable|null $updatedAt {default null}
 */
trait UpdatedAt
{

}
