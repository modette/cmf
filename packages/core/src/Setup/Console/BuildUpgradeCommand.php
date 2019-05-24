<?php declare(strict_types = 1);

namespace Modette\Core\Setup\Console;

use Modette\Core\Setup\SetupHelper;
use Modette\Core\Setup\WorkerManagerAccessor;
use Modette\Core\Setup\WorkerMode;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BuildUpgradeCommand extends Command
{

	/** @var string */
	protected static $defaultName = 'modette:build:upgrade';

	/** @var WorkerManagerAccessor */
	private $managerAccessor;

	/** @var bool */
	private $debugMode;

	/** @var bool */
	private $developmentServer;

	public function __construct(WorkerManagerAccessor $managerAccessor, bool $debugMode, bool $developmentServer)
	{
		parent::__construct();
		$this->managerAccessor = $managerAccessor;
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
		$workers = $this->managerAccessor->get()->getWorkers();
		if ($workers === []) {
			$output->writeln('<comment>No workers available for build upgrade</comment>');
			return 0;
		}

		$helper = new SetupHelper(WorkerMode::UPGRADE(), $this->debugMode, $this->developmentServer, $this->getApplication(), $output);
		foreach ($workers as $worker) {
			$output->writeln(sprintf('Running %s worker', $worker->getName()));
			$worker->work($helper);
		}

		$output->writeln('<success>Upgrade complete</success>');

		return 0;
	}

}
