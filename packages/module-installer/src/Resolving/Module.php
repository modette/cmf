<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Resolving;

use Modette\ModuleInstaller\Configuration\PackageConfiguration;

final class Module
{

	/** @var PackageConfiguration */
	private $configuration;

	/** @var Module[] */
	private $dependents = [];

	public function __construct(PackageConfiguration $configuration)
	{
		$this->configuration = $configuration;
	}

	public function getConfiguration(): PackageConfiguration
	{
		return $this->configuration;
	}

	/**
	 * @param Module[] $dependents
	 */
	public function setDependents(array $dependents): void
	{
		$this->dependents = $dependents;
	}

	/**
	 * @return Module[]
	 */
	public function getDependents(): array
	{
		return $this->dependents;
	}

}
