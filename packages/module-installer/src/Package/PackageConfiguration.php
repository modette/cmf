<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Package;

use Composer\Package\PackageInterface;

final class PackageConfiguration
{

	/** @var float */
	private $version;

	/** @var LoaderConfiguration|null */
	private $loader;

	/** @var FileConfiguration[] */
	private $files;

	/** @var string[] */
	private $ignoredPackages;

	/** @var PackageInterface */
	private $package;

	/**
	 * @param mixed[] $configuration
	 */
	public function __construct(array $configuration, PackageInterface $package)
	{
		$this->version = $configuration['version'];
		$this->files = $this->normalizeFiles($configuration['files']);
		$this->loader = $configuration['loader'] !== null ? new LoaderConfiguration($configuration['loader']) : null;
		$this->ignoredPackages = $configuration['ignored'];
		$this->package = $package;
	}

	public function getVersion(): float
	{
		return $this->version;
	}

	public function getLoader(): ?LoaderConfiguration
	{
		return $this->loader;
	}

	/**
	 * @return FileConfiguration[]
	 */
	public function getFiles(): array
	{
		return $this->files;
	}

	/**
	 * @return string[]
	 */
	public function getIgnoredPackages(): array
	{
		return $this->ignoredPackages;
	}

	public function getPackage(): PackageInterface
	{
		return $this->package;
	}

	/**
	 * @param mixed[] $files
	 * @return FileConfiguration[]
	 */
	private function normalizeFiles(array $files): array
	{
		$normalized = [];

		foreach ($files as $file) {
			if (is_string($file)) {
				$file = [
					'file' => $file,
					'parameters' => [],
				];
			}

			$normalized[] = new FileConfiguration($file);
		}

		return $normalized;
	}

}
