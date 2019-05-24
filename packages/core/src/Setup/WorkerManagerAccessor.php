<?php declare(strict_types = 1);

namespace Modette\Core\Setup;

interface WorkerManagerAccessor
{

	public function get(): WorkerManager;

}
