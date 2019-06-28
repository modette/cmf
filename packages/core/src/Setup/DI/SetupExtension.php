<?php declare(strict_types = 1);

namespace Modette\Core\Setup\DI;

use Contributte\DI\Helper\ExtensionDefinitionsHelper;
use Modette\Core\Setup\Console\BuildReloadCommand;
use Modette\Core\Setup\Console\BuildUpgradeCommand;
use Modette\Core\Setup\WorkerManager;
use Modette\Core\Setup\WorkerManagerAccessor;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\Statement;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use stdClass;

/**
 * @property-read stdClass $config
 */
class SetupExtension extends CompilerExtension
{

	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'workers' => Expect::arrayOf(
				Expect::structure([
					'worker' => Expect::anyOf(Expect::string(), Expect::array(), Expect::type(Statement::class))->required(),
					'priority' => Expect::int(100),
				])
			),
		]);
	}

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->config;
		$definitionsHelper = new ExtensionDefinitionsHelper($this->compiler);

		$manager = $builder->addDefinition($this->prefix('manager'))
			->setFactory(WorkerManager::class);

		$managerAccessor = $builder->addAccessorDefinition($this->prefix('managerAccessor'))
			->setImplement(WorkerManagerAccessor::class);

		foreach ($config->workers as $workerName => $workerConfig) {
			$workerPrefix = $this->prefix('worker.' . $workerName);
			$workerDefinition = $definitionsHelper->getDefinitionFromConfig($workerConfig->worker, $workerPrefix);
			$manager->addSetup('addWorker', [
				$workerDefinition,
				$workerConfig->priority,
			]);
		}

		$debugMode = $builder->parameters['debugMode'];
		$developmentServer = $builder->parameters['server']['development'];

		$builder->addDefinition($this->prefix('command.buildReload'))
			->setFactory(BuildReloadCommand::class, [$managerAccessor, $debugMode, $developmentServer]);

		$builder->addDefinition($this->prefix('command.buildUpgrade'))
			->setFactory(BuildUpgradeCommand::class, [$managerAccessor, $debugMode, $developmentServer]);
	}

}
