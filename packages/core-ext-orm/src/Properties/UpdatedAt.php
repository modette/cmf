<?php declare(strict_types = 1);

namespace Modette\Orm\Properties;

use Nextras\Dbal\Utils\DateTimeImmutable;

/**
 * @BeforePersist(Modette\Orm\Listeners\UpdatedAtListener)
 * @property-read DateTimeImmutable|null $updatedAt {default null}
 */
trait UpdatedAt
{

}
