<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Package;

final class FileConfiguration
{

	/** @var string */
	private $file;

	/**
	 * @param mixed[] $configuration
	 */
	public function __construct(array $configuration)
	{
		$this->file = $configuration['file'];
	}

	public function getFile(): string
	{
		return $this->file;
	}

}
