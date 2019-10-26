<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Monorepo;

use Composer\Package\CompletePackage;

final class SimulatedPackage extends CompletePackage
{

	/** @var string */
	private $packageDirectory;

	/** @var string */
	private $parentName;

	public function getPackageDirectory(): string
	{
		return $this->packageDirectory;
	}

	public function setPackageDirectory(string $packageDirectory): void
	{
		$this->packageDirectory = $packageDirectory;
	}

	public function getParentName(): string
	{
		return $this->parentName;
	}

	public function setParentName(string $parentName): void
	{
		$this->parentName = $parentName;
	}

}
