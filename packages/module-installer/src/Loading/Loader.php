<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Loading;

use Exception;

abstract class Loader
{

	/** @var mixed[] */
	protected $files = [];

	/**
	 * @param mixed[] $parameters
	 * @return string[]
	 */
	public function getConfigFiles(array $parameters): array
	{
		$resolved = [];

		foreach ($this->files as $file) {
			foreach ($file['parameters'] as $parameterName => $parameterValue) {
				if (!array_key_exists($parameterName, $parameters)) {
					throw new Exception(sprintf(
						'Parameter \'%s\' not available, cannot check config file \'%s\' availability. Be beware of fact that dynamic parameters are not supported.',
						$parameterName,
						$file['file']
					));
				}

				// One of parameters does not match, config file not included
				if ($parameterValue !== $parameters[$parameterName]) {
					continue 2;
				}
			}

			$resolved[] = $file['file'];
		}

		return $resolved;
	}

}
