<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Loading;

use Modette\Exceptions\Logic\InvalidStateException;

abstract class BaseLoader
{

	/** @var mixed[] */
	protected $schema = [];

	/** @var mixed[] */
	protected $modulesMeta = [];

	final public function __construct()
	{
		// Disallow method override so it's safe to create magically
	}

	/**
	 * @param mixed[] $parameters
	 * @return string[]
	 */
	public function loadConfigFiles(string $rootDir, array $parameters): array
	{
		$resolved = [];

		foreach ($this->schema as $item) {
			foreach ($item['parameters'] ?? [] as $parameterName => $parameterValue) {
				if (!array_key_exists($parameterName, $parameters)) {
					throw new InvalidStateException(sprintf(
						'Parameter \'%s\' not available, cannot check config file \'%s\' availability. Be beware of fact that dynamic parameters are not supported.',
						$parameterName,
						$item['file']
					));
				}

				// One of parameters does not match, config file not included
				if ($parameterValue !== $parameters[$parameterName]) {
					continue 2;
				}
			}

			$resolved[] = $rootDir . '/' . $item['file'];
		}

		return $resolved;
	}

	/**
	 * @return mixed[]
	 */
	public function loadModulesMeta(string $rootDir): array
	{
		$meta = [];

		foreach ($this->modulesMeta as $moduleName => $moduleMeta) {
			$dir = $moduleMeta['dir'];
			$moduleMeta['dir'] = $dir === '' ? $rootDir : $rootDir . '/' . $dir;

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
	 * @return mixed[]
	 * @internal
	 */
	public function getModulesMeta(): array
	{
		return $this->modulesMeta;
	}

}
