<?php declare(strict_types = 1);

namespace Modette\Sql\Setup\Worker;

use Modette\Core\Setup\SetupMeta;
use Modette\Core\Setup\Worker\Worker;
use Modette\Core\Setup\WorkerMode;

class MigrationsWorker implements Worker
{

	public function work(SetupMeta $meta): void
	{
		if ($meta->getWorkerMode()->is(WorkerMode::UPGRADE())) {
			if ($meta->isDevelopmentServer()) { // phpcs:ignore
				//migrations::continue
			} else { // phpcs:ignore
				//migrations:continue --production
			}
		} else {
			if ($meta->isDevelopmentServer()) { // phpcs:ignore
				//migrations::reset
			} else { // phpcs:ignore
				//migrations:reset --production
			}
		}
	}

}
