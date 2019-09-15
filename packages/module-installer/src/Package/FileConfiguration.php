<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Package;

final class FileConfiguration
{

	/** @var string */
	private $file;

	/** @var mixed[] */
	private $parameters;

	/**
	 * @param mixed[] $configuration
	 */
	public function __construct(array $configuration)
	{
		$this->file = $configuration['file'];
		$this->parameters = $configuration['parameters'];
	}

	public function getFile(): string
	{
		return $this->file;
	}

	/**
	 * @return mixed[]
	 */
	public function getParameters(): array
	{
		return $this->parameters;
	}

}
