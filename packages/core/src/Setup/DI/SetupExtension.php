<?php declare(strict_types = 1);

namespace Modette\Core\Setup\DI;

use Modette\Core\Setup\Console\BuildReloadCommand;
use Modette\Core\Setup\Console\BuildUpgradeCommand;
use Modette\Core\Setup\WorkerManager;
use Nette\DI\CompilerExtension;

class SetupExtension extends CompilerExtension
{

	/** @var mixed[] */
	private $defaults = [
		'workers' => [],
	];

	/** @var mixed[] */
	private $workerDefaults = [
		'worker' => null,
		'priority' => 100,
	];

	public function loadConfiguration(): void
	{
		$config = $this->validateConfig($this->defaults);
		$builder = $this->getContainerBuilder();

		$manager = $builder->addDefinition($this->prefix('manager'))
			->setFactory(WorkerManager::class);

		foreach ($config['workers'] as $workerConfig) {
			$workerConfig = $this->validateConfig($this->workerDefaults, $workerConfig);

			$manager->addSetup('addWorker', [
				$workerConfig['worker'],
				$workerConfig['priority'],
			]);
		}

		$debugMode = $builder->parameters['debugMode'];
		$developmentServer = $builder->parameters['server']['development'];

		$builder->addDefinition($this->prefix('command.buildReload'))
			->setFactory(BuildReloadCommand::class, [$manager, $debugMode, $developmentServer]);

		$builder->addDefinition($this->prefix('command.buildUpgrade'))
			->setFactory(BuildUpgradeCommand::class, [$manager, $debugMode, $developmentServer]);
	}

}
