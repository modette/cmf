<?php declare(strict_types = 1);

namespace Modette\Core\Setup\Console;

use Modette\Core\Setup\DriverManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BuildReloadCommand extends Command
{

	/** @var string */
	protected static $defaultName = 'modette:build:reload';

	/** @var DriverManager */
	private $manager;

	public function __construct(DriverManager $manager)
	{
		parent::__construct();
		$this->manager = $manager;
	}

	protected function configure(): void
	{
		$this->setName(static::$defaultName);
		$this->setDescription('Rebuild application requirements from initial state');
	}

	protected function execute(InputInterface $input, OutputInterface $output): void
	{
		foreach ($this->manager->getDrivers() as $driver) {
			$driver->reload();
		}

		$output->writeln('TODO - modette:build:reload');
	}

}
