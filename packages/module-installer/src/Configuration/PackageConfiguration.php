<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Configuration;

use Composer\Package\PackageInterface;

final class PackageConfiguration
{

	public const VERSION_OPTION = 'version';
	public const LOADER_OPTION = 'loader';
	public const FILES_OPTION = 'files';
	public const IGNORE_OPTION = 'ignore';
	public const SIMULATED_MODULES_OPTION = 'simulated-modules';

	/** @var string */
	private $schemaPath;

	/** @var string */
	private $schemaFile;

	/** @var float */
	private $version;

	/** @var LoaderConfiguration|null */
	private $loader;

	/** @var FileConfiguration[] */
	private $files;

	/** @var string[] */
	private $ignoredPackages;

	/** @var string[] */
	private $simulatedModules;

	/** @var PackageInterface */
	private $package;

	/**
	 * @param mixed[] $configuration
	 */
	public function __construct(array $configuration, PackageInterface $package, string $schemaFile)
	{
		$lastSlashPosition = strrpos($schemaFile, '/');
		$this->schemaPath = $lastSlashPosition === false ? '' : substr($schemaFile, 0, $lastSlashPosition);
		$this->schemaFile = $schemaFile;
		$this->version = $configuration[self::VERSION_OPTION];
		$this->files = $this->normalizeFiles($configuration[self::FILES_OPTION]);
		$this->loader = $configuration[self::LOADER_OPTION] !== null ? new LoaderConfiguration($configuration[self::LOADER_OPTION]) : null;
		$this->ignoredPackages = $configuration[self::IGNORE_OPTION];
		$this->simulatedModules = $configuration[self::SIMULATED_MODULES_OPTION];
		$this->package = $package;
	}

	public function getSchemaPath(): string
	{
		return $this->schemaPath;
	}

	public function getSchemaFile(): string
	{
		return $this->schemaFile;
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
	 * @return string[]
	 */
	public function getSimulatedModules(): array
	{
		return $this->simulatedModules;
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
					FileConfiguration::FILE_OPTION => $file,
					FileConfiguration::PARAMETERS_OPTION => [],
					FileConfiguration::PACKAGES_OPTION => [],
				];
			}

			$normalized[] = new FileConfiguration($file);
		}

		return $normalized;
	}

}
