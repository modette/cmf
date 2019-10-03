<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Configuration;

final class LoaderConfiguration
{

	public const FILE_OPTION = 'file';
	public const CLASS_OPTION = 'class';

	/** @var string */
	private $file;

	/** @var string */
	private $class;

	/**
	 * @param mixed[] $configuration
	 */
	public function __construct(array $configuration)
	{
		$this->file = $configuration[self::FILE_OPTION];
		$this->class = $configuration[self::CLASS_OPTION];
	}

	public function getFile(): string
	{
		return $this->file;
	}

	public function getClass(): string
	{
		return $this->class;
	}

}
