<?php declare(strict_types = 1);

namespace Modette\Core\Setup\Driver;

class MigrationsDriver implements Driver
{

	/** @var bool */
	private $isProduction;

	public function __construct(bool $isProduction = false)
	{
		$this->isProduction = $isProduction;
	}

	public function upgrade(): void
	{
		if ($this->isProduction) {
			return;
			//migrations:continue --production
		} else {
			return;
			//migrations::continue
		}
	}

	public function reload(): void
	{
		if ($this->isProduction) {
			return;
			//migrations:reset --production
		} else {
			return;
			//migrations::reset
		}
	}

}
