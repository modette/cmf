<?php declare(strict_types = 1);

namespace Modette\Core\Setup\Console;

use Modette\Core\Exception\Logic\InvalidStateException;
use Modette\Core\Setup\SetupMeta;
use Modette\Core\Setup\WorkerManager;
use Modette\Core\Setup\WorkerMode;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BuildReloadCommand extends Command
{

	/** @var string */
	protected static $defaultName = 'modette:build:reload';

	/** @var WorkerManager */
	private $manager;

	/** @var bool */
	private $debugMode;

	/** @var bool */
	private $developmentServer;

	public function __construct(WorkerManager $manager, bool $debugMode, bool $developmentServer)
	{
		parent::__construct();
		$this->manager = $manager;
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

		$meta = new SetupMeta(WorkerMode::RELOAD(), $this->debugMode, $this->developmentServer);
		foreach ($this->manager->getWorkers() as $worker) {
			$worker->work($meta);
		}

		$output->writeln('TODO - modette:build:reload');
		return 0;
	}

}
