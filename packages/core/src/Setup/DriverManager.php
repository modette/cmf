<?php declare(strict_types = 1);

namespace Modette\Core\Setup;

use Modette\Core\Setup\Driver\Driver;

class DriverManager
{

	/** @var mixed[] */
	private $drivers = [];

	public function addDriver(Driver $driver, int $priority): self
	{
		$this->drivers[] = [
			'driver' => $driver,
			'priority' => $priority,
		];
		return $this;
	}

	/**
	 * @return Driver[]
	 */
	public function getDrivers(): array
	{
		// Sort by priority
		uasort($this->drivers, function ($a, $b) {
			$p1 = $a['priority'];
			$p2 = $b['priority'];
			if ($p1 === $p2) {
				return 0;
			}
			return ($p1 < $p2) ? -1 : 1;
		});

		return array_column($this->drivers, 'driver');
	}

}
