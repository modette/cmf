<?php declare(strict_types = 1);

namespace Modette\Sql\DI;

use Nette\DI\CompilerExtension;
use Nextras\Dbal\Bridges\NetteTracy\BluescreenQueryPanel;
use Nextras\Dbal\Bridges\NetteTracy\ConnectionPanel;
use Nextras\Dbal\Connection;
use Nextras\Dbal\Drivers\IDriver;
use Psr\Log\LoggerInterface;

class DbalExtension extends CompilerExtension
{

	/** @var mixed[] */
	private $defaults = [
		'connections' => [],
		'debug' => false,
		'panelQueryExplain' => true,
	];

	/** @var mixed[] */
	private $connectionDefaults = [
		'autowired' => false,

		'driver' => null, // mysql, mysqli, pgsql, sqlsrv
		'host' => null,
		'port' => null,
		'username' => null,
		'password' => null,
		'database' => null,
		'connectionTz' => IDriver::TIMEZONE_AUTO_PHP_NAME,
		'nestedTransactionsWithSavepoint' => true,
		'sqlProcessorFactory' => null,

		// mysql only
		'charset' => null,
		'sqlMode' => 'TRADITIONAL',
		'unix_socket' => null,
		'flags' => null,

		// pgsql only
		'searchPath' => null,
		'hostaddr' => null,
		'connection_timeout' => null,
		'options' => null,
		'sslmode' => null,
		'service' => null,
	];

	public function loadConfiguration(): void
	{
		$config = $this->validateConfig($this->defaults);
		$builder = $this->getContainerBuilder();

		foreach ($config['connections'] as $connectionName => $connectionConfig) {
			$connectionConfig = $this->validateConfig($this->connectionDefaults, $connectionConfig, $this->prefix('connection.' . $connectionName));

			if ($connectionConfig['driver'] === 'mysql') { // same configuration for dbal and migrations
				$connectionConfig['driver'] = 'mysqli';
			}

			$autowired = $connectionConfig['autowired'];
			unset($connectionConfig['autowired']); // remove from connection config compile-time only values

			$definition = $builder->addDefinition($this->prefix('connection.' . $connectionName))
				->setFactory(Connection::class, [
					'config' => $connectionConfig,
				])
				->setAutowired($autowired)
				->addSetup(
					'?->onQuery[] = function (\Nextras\Dbal\Connection $connection, string $query, float $time): void {
	?->info("Query:" . $query, ["time" => $time, "connection" => ?]);
}',
					[
						'@self',
						'@' . LoggerInterface::class,
						$connectionName,
					]
				);

			// TODO - panel for all connections
			if ($config['debug'] === true) {
				$definition->addSetup('@Tracy\BlueScreen::addPanel', [BluescreenQueryPanel::class . '::renderBluescreenPanel']);
				$definition->addSetup(ConnectionPanel::class . '::install', ['@self', $config['panelQueryExplain']]);
			}
		}
	}

}
