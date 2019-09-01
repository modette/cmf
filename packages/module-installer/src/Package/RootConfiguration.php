<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Package;

final class RootConfiguration
{

	/** @var float */
	private $version;

	/** @var FileConfiguration[] */
	private $files;

	/**
	 * @param mixed[] $configuration
	 */
	public function __construct(array $configuration)
	{
		$this->version = $configuration['version'];
		$this->files = $this->normalizeFiles($configuration['files']);
	}

	public function getVersion(): float
	{
		return $this->version;
	}

	/**
	 * @return FileConfiguration[]
	 */
	public function getFiles(): array
	{
		return $this->files;
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
