<?php declare(strict_types = 1);

namespace Modette\Core\Setup;

use Modette\Core\Setup\Worker\Worker;

class WorkerManager
{

	/** @var mixed[] */
	private $workers = [];

	public function addWorker(Worker $worker, int $priority): self
	{
		$this->workers[] = [
			'worker' => $worker,
			'priority' => $priority,
		];
		return $this;
	}

	/**
	 * @return Worker[]
	 */
	public function getWorkers(): array
	{
		// Sort by priority
		uasort($this->workers, function ($a, $b) {
			$p1 = $a['priority'];
			$p2 = $b['priority'];
			if ($p1 === $p2) {
				return 0;
			}
			return ($p1 < $p2) ? -1 : 1;
		});

		return array_column($this->workers, 'worker');
	}

}
