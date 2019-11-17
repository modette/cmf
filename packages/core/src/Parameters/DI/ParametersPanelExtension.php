<?php declare(strict_types = 1);

namespace Modette\Core\Parameters\DI;

use Modette\Core\Parameters\Tracy\ParametersPanel;
use Nette\DI\CompilerExtension;
use Nette\PhpGenerator\ClassType;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use stdClass;

/**
 * @property-read stdClass $config
 */
final class ParametersPanelExtension extends CompilerExtension
{

	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'debug' => Expect::bool(false),
		]);
	}

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->config;

		if ($config->debug) {
			$builder->addDefinition($this->prefix('panel'))
				->setFactory(ParametersPanel::class)
				->setAutowired(false);
		}
	}

	public function afterCompile(ClassType $class): void
	{
		$config = $this->config;
		$initialize = $class->getMethod('initialize');

		if ($config->debug) {
			$initialize->addBody('$this->getService(?)->addPanel($this->getService(?));', [
				'tracy.bar',
				$this->prefix('panel'),
			]);
		}
	}

}
