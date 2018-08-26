<?php declare(strict_types = 1);

namespace Modette\Core\Setup\Driver;

interface Driver
{

	public function upgrade(): void;

	public function reload(): void;

}
