<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Configuration;

final class FileConfiguration
{

	public const FILE_OPTION = 'file';
	public const PARAMETERS_OPTION = 'parameters';
	public const PACKAGES_OPTION = 'packages';
	public const PRIORITY_OPTION = 'priority';

	public const PRIORITY_DEFAULT = self::PRIORITY_VALUE_NORMAL;

	public const PRIORITY_VALUE_LOW = 'low';
	public const PRIORITY_VALUE_NORMAL = 'normal';
	public const PRIORITY_VALUE_HIGH = 'high';
	public const PRIORITIES = [
		self::PRIORITY_VALUE_LOW,
		self::PRIORITY_VALUE_NORMAL,
		self::PRIORITY_VALUE_HIGH,
	];

	/** @var string */
	private $file;

	/** @var mixed[] */
	private $parameters;

	/** @var string[] */
	private $packages;

	/** @var string */
	private $priority;

	/**
	 * @param mixed[] $configuration
	 */
	public function __construct(array $configuration)
	{
		$this->file = $configuration[self::FILE_OPTION];
		$this->parameters = $configuration[self::PARAMETERS_OPTION];
		$this->packages = $configuration[self::PACKAGES_OPTION];
		$this->priority = $configuration[self::PRIORITY_OPTION];
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

	public function getPriority(): string
	{
		return $this->priority;
	}

}
