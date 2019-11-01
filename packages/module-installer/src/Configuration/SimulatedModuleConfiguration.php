<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Configuration;

final class SimulatedModuleConfiguration
{

	public const NAME_OPTION = 'name';
	public const PATH_OPTION = 'path';
	public const OPTIONAL_OPTION = 'optional';

	public const OPTIONAL_DEFAULT = false;

	/** @var string */
	private $name;

	/** @var string */
	private $path;

	/** @var bool */
	private $optional;

	/**
	 * @param mixed[] $configuration
	 */
	public function __construct(array $configuration)
	{
		$this->name = $configuration[self::NAME_OPTION];
		$this->path = $configuration[self::PATH_OPTION];
		$this->optional = $configuration[self::OPTIONAL_OPTION];
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getPath(): string
	{
		return $this->path;
	}

	public function isOptional(): bool
	{
		return $this->optional;
	}

}
