<?php declare(strict_types = 1);

namespace Modette\Core\Setup\Worker;

use Modette\Core\Setup\SetupHelper;
use Symfony\Component\Console\Input\ArrayInput;

class CacheCleanWorker implements Worker
{

	public function getName(): string
	{
		return 'cache clean';
	}

	public function work(SetupHelper $helper): void
	{
		$command = $helper->getApplication()->find('contributte:cache:clean');
		$command->run(new ArrayInput([]), $helper->getOutput());
	}

}
