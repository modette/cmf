<?php declare(strict_types = 1);

namespace Modette\Core\Setup\Console;

use Modette\Core\Setup\SetupHelper;
use Modette\Core\Setup\WorkerManagerAccessor;
use Modette\Core\Setup\WorkerMode;
use Modette\Exceptions\Logic\InvalidStateException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

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

		$style = new SymfonyStyle($input, $output);
		$workers = $this->managerAccessor->get()->getWorkers();

		if ($workers === []) {
			$style->warning('No workers available for build reload');

			return 0;
		}

		$application = $this->getApplication();
		assert($application !== null);

		$meta = new SetupHelper(WorkerMode::RELOAD(), $this->debugMode, $this->developmentServer, $application, $output);

		foreach ($workers as $worker) {
			$style->note(sprintf('Running %s worker', $worker->getName()));
			$worker->work($meta);
		}

		$style->success('Reload complete');

		return 0;
	}

}
