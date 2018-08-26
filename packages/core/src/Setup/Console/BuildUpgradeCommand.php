<?php declare(strict_types = 1);

namespace Modette\Core\Setup\Console;

use Modette\Core\Setup\DriverManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BuildUpgradeCommand extends Command
{

	/** @var string */
	protected static $defaultName = 'modette:build:upgrade';

	/** @var DriverManager */
	private $manager;

	public function __construct(DriverManager $manager)
	{
		//TODO - předat závislost lazy - přes helper? accessor?
		parent::__construct();
		$this->manager = $manager;
	}

	protected function configure(): void
	{
		$this->setName(static::$defaultName);
		$this->setDescription('Update application requirements');
	}

	protected function execute(InputInterface $input, OutputInterface $output): void
	{
		foreach ($this->manager->getDrivers() as $driver) {
			$driver->upgrade();
		}

		$output->writeln('TODO - modette:build:upgrade');
	}

}
