<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Configuration;

final class FileConfiguration
{

	/** @var string */
	private $file;

	/** @var mixed[] */
	private $parameters;

	/** @var string[] */
	private $packages;

	/**
	 * @param mixed[] $configuration
	 */
	public function __construct(array $configuration)
	{
		$this->file = $configuration['file'];
		$this->parameters = $configuration['parameters'];
		$this->packages = $configuration['packages'];
	}

	public function getFile(): string
	{
		return $this->file;
	}

	/**
	 * @return mixed[]
	 */
	public function getRequiredParameters(): array
	{
		return $this->parameters;
	}

	/**
	 * @return string[]
	 */
	public function getRequiredPackages(): array
	{
		return $this->packages;
	}

}
