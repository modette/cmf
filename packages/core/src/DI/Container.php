<?php declare(strict_types = 1);

namespace Modette\Core\DI;

use Modette\Exceptions\Logic\DeprecatedException;
use Nette\DI\Container as NetteContainer;

class Container extends NetteContainer
{

	/**
	 * @return void
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
	 */
	public function initialize()
	{
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingTraversableReturnTypeHintSpecification
	 */
	public function findByTag(string $tag): array
	{
		$this->deprecated(__METHOD__);
	}

	private function deprecated(string $method): void
	{
		throw new DeprecatedException(sprintf(
			'Method "%s::%s" is deprecated in behalf of compile-time service resolution and working with service names (like "getService($name)").',
			NetteContainer::class,
			$method
		));
	}

}
