<?php declare(strict_types = 1);

namespace Modette\Core\DI;

class Parameters
{

	/** @var mixed[] */
	private $parameters;

	/**
	 * @param mixed[] $parameters
	 */
	public function __construct(array $parameters)
	{
		$this->parameters = $parameters;
	}

	/**
	 * @return mixed[]
	 */
	public function getAll(): array
	{
		return $this->parameters;
	}

	public function isDebugMode(): bool
	{
		return $this->parameters['debugMode'];
	}

	/**
	 * @return mixed[]
	 */
	public function getStorage(): array
	{
		return $this->parameters['storage'];
	}

	/**
	 * @return mixed[]
	 */
	public function getServer(): array
	{
		return $this->parameters['server'];
	}

	public function isDevelopmentServer(): bool
	{
		return $this->parameters['server']['development'];
	}

}
