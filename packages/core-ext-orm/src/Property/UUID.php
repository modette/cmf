<?php declare(strict_types = 1);

namespace Modette\Orm\Property;

use Ramsey\Uuid\Generator\CombGenerator;
use Ramsey\Uuid\UuidFactory;

/**
 * @property-read string $id {primary}
 */
trait UUID
{

	public function onCreate(): void
	{
		$factory = new UuidFactory();

		$generator = new CombGenerator(
			$factory->getRandomGenerator(),
			$factory->getNumberConverter()
		);

		$factory->setRandomGenerator($generator);

		$this->id = $factory->uuid4()->toString();
	}

}
