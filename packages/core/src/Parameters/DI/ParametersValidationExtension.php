<?php declare(strict_types = 1);

namespace Modette\Core\Parameters\DI;

use Modette\Core\Boot\Configurator;
use Modette\Exceptions\Logic\InvalidStateException;
use Nette\DI\CompilerExtension;

class ParametersValidationExtension extends CompilerExtension
{

	public function loadConfiguration(): void
	{
		$parameters = $this->getContainerBuilder()->parameters;

		$this->checkParameterExistence($parameters, 'debugMode');
		$this->checkParameterExistence($parameters, 'consoleMode');
		$this->checkDirectories($parameters);
		$this->checkModulesMeta($parameters);
	}

	/**
	 * @param mixed[] $parameters
	 */
	private function checkDirectories(array $parameters): void
	{
		// TODO - appDir validation requires proper scope configuration in monorepo
		$dirs = ['rootDir', /*'appDir',*/ 'logDir', 'tempDir', 'vendorDir'];

		foreach ($dirs as $key) {
			$dir = $this->checkParameterExistence($parameters, $key);
			$this->checkDirectoryExistence($dir, $key);
		}
	}

	/**
	 * @param mixed[] $parameters
	 */
	private function checkModulesMeta(array $parameters): void
	{
		$modules = $this->checkParameterExistence($parameters, 'modules');

		foreach ($modules as $moduleName => $moduleMeta) {
			$dir = $moduleMeta['dir'];
			$key = 'modules > ' . $moduleName . ' > dir';
			$this->checkDirectoryExistence($dir, $key);
		}
	}

	private function checkDirectoryExistence(string $dir, string $key): void
	{
		if (!is_dir($dir)) {
			throw new InvalidStateException(sprintf(
				'Parameter \'%s\' must contain path to an existing directory, \'%s\' given.',
				$key,
				$dir
			));
		}
	}

	/**
	 * @param mixed[] $parameters
	 * @return mixed
	 */
	private function checkParameterExistence(array $parameters, string $parameterName)
	{
		if (!isset($parameters[$parameterName])) {
			throw new InvalidStateException(sprintf(
				'Mandatory parameter \'%s\' is missing. %s',
				$parameterName,
				$this->getSuggestion()
			));
		}

		return $parameters[$parameterName];
	}

	private function getSuggestion(): string
	{
		return sprintf('Use \'%s\' to load DI container.', Configurator::class);
	}

}
