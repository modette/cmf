<?php declare(strict_types = 1);

namespace Modette\Core\Setup\Console;

use Modette\Core\Setup\SetupMeta;
use Modette\Core\Setup\WorkerManager;
use Modette\Core\Setup\WorkerMode;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BuildUpgradeCommand extends Command
{

	/** @var string */
	protected static $defaultName = 'modette:build:upgrade';

	/** @var WorkerManager */
	private $manager;

	/** @var bool */
	private $debugMode;

	/** @var bool */
	private $developmentServer;

	public function __construct(WorkerManager $manager, bool $debugMode, bool $developmentServer)
	{
		//TODO - předat závislosti přes accessor
		parent::__construct();
		$this->manager = $manager;
		$this->debugMode = $debugMode;
		$this->developmentServer = $developmentServer;
	}

	protected function configure(): void
	{
		$this->setName(static::$defaultName);
		$this->setDescription('Update application requirements');
	}

	protected function execute(InputInterface $input, OutputInterface $output): ?int
	{
		$meta = new SetupMeta(WorkerMode::UPGRADE(), $this->debugMode, $this->developmentServer);
		foreach ($this->manager->getWorkers() as $worker) {
			$worker->work($meta);
		}

		$output->writeln('TODO - modette:build:upgrade');
		return 0;
	}

}
