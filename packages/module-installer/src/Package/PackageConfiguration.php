<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Package;

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

	/**
	 * @param mixed[] $configuration
	 */
	public function __construct(array $configuration)
	{
		$this->version = $configuration['version'];
		$this->files = $this->normalizeFiles($configuration['files']);
		$this->loader = $configuration['loader'] !== null ? new LoaderConfiguration($configuration['loader']) : null;
		$this->ignoredPackages = $configuration['ignored'];
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
				];
			}

			$normalized[] = new FileConfiguration($file);
		}

		return $normalized;
	}

}
