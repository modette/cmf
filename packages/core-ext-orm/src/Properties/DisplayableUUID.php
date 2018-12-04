<?php declare(strict_types = 1);

namespace Modette\Orm\Properties;

use Ramsey\Uuid\Generator\CombGenerator;
use Ramsey\Uuid\UuidFactory;

/**
 * @property-read string $displayId
 */
trait DisplayableUUID
{

	public function onCreate(): void
	{
		$factory = new UuidFactory();

		$generator = new CombGenerator(
			$factory->getRandomGenerator(),
			$factory->getNumberConverter()
		);

		$factory->setRandomGenerator($generator);

		$this->displayId = $factory->uuid4()->toString();
	}

}
