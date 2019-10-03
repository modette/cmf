<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Configuration;

final class FileConfiguration
{

	public const FILE_OPTION = 'file';
	public const PARAMETERS_OPTION = 'parameters';
	public const PACKAGES_OPTION = 'packages';

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
		$this->file = $configuration[self::FILE_OPTION];
		$this->parameters = $configuration[self::PARAMETERS_OPTION];
		$this->packages = $configuration[self::PACKAGES_OPTION];
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
