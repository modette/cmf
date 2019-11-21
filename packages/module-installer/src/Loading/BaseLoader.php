<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Loading;

use Modette\Exceptions\Logic\InvalidArgumentException;
use Modette\ModuleInstaller\Configuration\PackageConfiguration;
use Modette\ModuleInstaller\Plugin;

abstract class BaseLoader
{

	public const SCHEMA_ITEM_FILE = 'file';
	public const SCHEMA_ITEM_SWITCHES = 'switches';
	public const META_ITEM_DIR = 'dir';

	/** @var mixed[] */
	protected $schema = [];

	/** @var bool[] */
	protected $switches = [];

	/** @var mixed[] */
	protected $modulesMeta = [];

	final public function __construct()
	{
		// Disallow method override so it's safe to create magically
	}

	/**
	 * @return string[]
	 */
	public function loadConfigFiles(string $rootDir): array
	{
		$resolved = [];

		foreach ($this->schema as $item) {
			foreach ($item[self::SCHEMA_ITEM_SWITCHES] ?? [] as $switchName => $switchValue) {
				// One of switches values does not match, config file not included
				if ($switchValue !== $this->switches[$switchName]) {
					continue 2;
				}
			}

			$resolved[] = $rootDir . '/' . $item[self::SCHEMA_ITEM_FILE];
		}

		return $resolved;
	}

	public function configureSwitch(string $switch, bool $value): void
	{
		if (!isset($this->switches[$switch])) {
			throw new InvalidArgumentException(sprintf(
				'Switch \'%s\' is not defined by any of loaded \'%s\' in \'%s\' section.',
				$switch,
				Plugin::DEFAULT_FILE_NAME,
				PackageConfiguration::SWITCHES_OPTION
			));
		}

		$this->switches[$switch] = $value;
	}

	/**
	 * @return mixed[]
	 */
	public function loadModulesMeta(string $rootDir): array
	{
		$meta = [];

		foreach ($this->modulesMeta as $moduleName => $moduleMeta) {
			$dir = $moduleMeta[self::META_ITEM_DIR];
			$moduleMeta[self::META_ITEM_DIR] = $dir === '' ? $rootDir : $rootDir . '/' . $dir;

			$meta[$moduleName] = $moduleMeta;
		}

		return $meta;
	}

	/**
	 * @return mixed[]
	 * @internal
	 */
	public function getSchema(): array
	{
		return $this->schema;
	}

	/**
	 * @return bool[]
	 * @internal
	 */
	public function getSwitches(): array
	{
		return $this->switches;
	}

	/**
	 * @return mixed[]
	 * @internal
	 */
	public function getModulesMeta(): array
	{
		return $this->modulesMeta;
	}

}
