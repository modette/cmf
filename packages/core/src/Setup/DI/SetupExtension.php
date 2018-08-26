<?php declare(strict_types = 1);

namespace Modette\Core\Setup\DI;

use Modette\Core\Setup\Console\BuildReloadCommand;
use Modette\Core\Setup\Console\BuildUpgradeCommand;
use Modette\Core\Setup\DriverManager;
use Nette\DI\CompilerExtension;

class SetupExtension extends CompilerExtension
{

	/** @var mixed[] */
	private $defaults = [
		'drivers' => [],
	];

	/** @var mixed[] */
	private $driverDefaults = [
		'driver' => null,
		'priority' => 100,
	];

	public function loadConfiguration(): void
	{
		$config = $this->validateConfig($this->defaults);
		$builder = $this->getContainerBuilder();

		$manager = $builder->addDefinition($this->prefix('manager'))
			->setFactory(DriverManager::class);

		foreach ($config['drivers'] as $driverConfig) {
			$driverConfig = $this->validateConfig($this->driverDefaults, $driverConfig);

			$manager->addSetup('addDriver', [
				$driverConfig['driver'],
				$driverConfig['priority'],
			]);
		}

		$builder->addDefinition($this->prefix('command.buildReload'))
			->setFactory(BuildReloadCommand::class, [$manager]);

		$builder->addDefinition($this->prefix('command.buildUpgrade'))
			->setFactory(BuildUpgradeCommand::class, [$manager]);
	}

}
