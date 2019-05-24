<?php declare(strict_types = 1);

namespace Modette\Core\Setup\Console;

use Modette\Core\Exception\Logic\InvalidStateException;
use Modette\Core\Setup\SetupHelper;
use Modette\Core\Setup\WorkerManagerAccessor;
use Modette\Core\Setup\WorkerMode;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BuildReloadCommand extends Command
{

	/** @var string */
	protected static $defaultName = 'modette:build:reload';

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
		$this->setDescription('Rebuild application requirements from initial state');
	}

	protected function execute(InputInterface $input, OutputInterface $output): ?int
	{
		if (!$this->developmentServer) {
			throw new InvalidStateException('Cannot execute reload command on production server. Make sure that your server is configured as development server.');
		}

		$workers = $this->managerAccessor->get()->getWorkers();
		if ($workers === []) {
			$output->writeln('<comment>No workers available for build reload</comment>');
			return 0;
		}

		$meta = new SetupHelper(WorkerMode::RELOAD(), $this->debugMode, $this->developmentServer, $this->getApplication(), $output);
		foreach ($workers as $worker) {
			$output->writeln(sprintf('Running %s worker', $worker->getName()));
			$worker->work($meta);
		}

		$output->writeln('<success>Reload complete</success>');

		return 0;
	}

}
