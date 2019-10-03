<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Configuration;

final class LoaderConfiguration
{

	/** @var string */
	private $file;

	/** @var string */
	private $class;

	/**
	 * @param mixed[] $configuration
	 */
	public function __construct(array $configuration)
	{
		$this->file = $configuration['file'];
		$this->class = $configuration['class'];
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
