<?php declare(strict_types = 1);

namespace Modette\Core\Setup\Worker;

use Modette\Core\Setup\SetupMeta;
use Modette\Core\Setup\WorkerMode;

class CacheCleanWorker implements Worker
{

	public function work(SetupMeta $meta): void
	{
		if ($meta->getWorkerMode()->is(WorkerMode::UPGRADE())) { // phpcs:ignore
			//Smazat cache překladů (aby se načetly nové překlady)
		} else { // phpcs:ignore
			//contributte:cache:clean
		}
	}

}
