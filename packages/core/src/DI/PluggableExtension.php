<?php declare(strict_types = 1);

namespace Modette\Core\DI;

use Modette\Core\Exception\Logic\InvalidArgumentException;
use Nette\DI\CompilerExtension;
use Nette\DI\Config\Helpers as ConfigHelpers;
use Nette\DI\Helpers;

abstract class PluggableExtension extends CompilerExtension
{

	/** @var mixed[] */
	private $cachedConfig = [];

	/**
	 * @param mixed[] $defaults
	 * @return mixed[]
	 */
	protected function resolveConfig(array $defaults): array
	{
		if ($this->cachedConfig !== []) {
			return $this->cachedConfig;
		}

		$parameters = $this->getContainerBuilder()->parameters;
		return $this->cachedConfig = (array) ConfigHelpers::merge(
			Helpers::expand($this->config, $parameters),
			Helpers::expand($defaults, $parameters)
		);
	}

	protected function getExtensionByType(string $type): CompilerExtension
	{
		$extensions = $this->compiler->getExtensions($type);
		if ($extensions !== []) {
			return $extensions[0];
		}

		throw new InvalidArgumentException(sprintf(
			'Extension of type %s not found.',
			$type
		));
	}

}
