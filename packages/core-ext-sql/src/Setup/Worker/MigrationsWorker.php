<?php declare(strict_types = 1);

namespace Modette\Sql\Setup\Worker;

use Modette\Core\Setup\SetupHelper;
use Modette\Core\Setup\Worker\Worker;
use Modette\Core\Setup\WorkerMode;
use Symfony\Component\Console\Input\ArrayInput;

class MigrationsWorker implements Worker
{

	public function getName(): string
	{
		return 'migrations';
	}

	public function work(SetupHelper $helper): void
	{
		$commandName = $helper->getWorkerMode()->is(WorkerMode::UPGRADE())
			? 'migrations:continue'
			: 'migrations:reset';

		$command = $helper->getApplication()->find($commandName);
		$command->run(new ArrayInput([]), $helper->getOutput());
	}

}
