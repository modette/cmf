<?php declare(strict_types = 1);

namespace Modette\Sql\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\Statement;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Nextras\Dbal\Bridges\NetteTracy\BluescreenQueryPanel;
use Nextras\Dbal\Bridges\NetteTracy\ConnectionPanel;
use Nextras\Dbal\Connection;
use Nextras\Dbal\Drivers\IDriver;
use Psr\Log\LoggerInterface;
use stdClass;

/**
 * @property-read stdClass $config
 */
class DbalExtension extends CompilerExtension
{

	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'debug' => Expect::bool(false),
			'panelQueryExplain' => Expect::bool(true),
			'connections' => Expect::arrayOf(
				Expect::structure([
					'autowired' => Expect::bool(false),

					'driver' => Expect::anyOf('mysql', 'mysqli', 'pgsql', 'sqlsrv')->required(),
					'host' => Expect::string(),
					'port' => Expect::int(),
					'username' => Expect::string(),
					'password' => Expect::string(),
					'database' => Expect::string(),
					'connectionTz' => Expect::string(IDriver::TIMEZONE_AUTO_PHP_NAME),
					'nestedTransactionsWithSavepoint' => Expect::bool(true),
					'sqlProcessorFactory' => Expect::anyOf(Expect::string(), Expect::type(Statement::class)),

					// mysql only
					'charset' => Expect::string(),
					'sqlMode' => Expect::string('TRADITIONAL'),
					'unix_socket' => Expect::mixed(),
					'flags' => Expect::mixed(),

					// pgsql only
					'searchPath' => Expect::mixed(),
					'hostaddr' => Expect::mixed(),
					'connection_timeout' => Expect::mixed(),
					'options' => Expect::mixed(),
					'sslmode' => Expect::mixed(),
					'service' => Expect::mixed(),
				])->castTo('array')
			),
		]);
	}

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->config;

		foreach ($config->connections as $connectionName => $connectionConfig) {
			// Same configuration for dbal and migrations
			if ($connectionConfig['driver'] === 'mysql') {
				$connectionConfig['driver'] = 'mysqli';
			}

			$autowired = $connectionConfig['autowired'];
			// Remove from Connection config compile-time only values
			unset($connectionConfig['autowired']);

			// Connection expects empty values to be not set
			foreach ($connectionConfig as $key => $value) {
				if ($value === null) {
					unset($connectionConfig[$key]);
				}
			}

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
			if ($config->debug) {
				$definition->addSetup('@Tracy\BlueScreen::addPanel', [BluescreenQueryPanel::class . '::renderBluescreenPanel']);
				$definition->addSetup(ConnectionPanel::class . '::install', ['@self', $config->panelQueryExplain]);
			}
		}
	}

}
