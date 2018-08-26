<?php declare(strict_types = 1);

namespace Modette\Orm\Properties;

use Ramsey\Uuid\Uuid as RamseyUuid;

/**
 * @property-read string $id {primary}
 */
trait UUID
{

	public function onCreate(): void
	{
		// TODO - I feel like this is hacky solution
		$this->id = RamseyUuid::uuid4()->toString();
	}

}
