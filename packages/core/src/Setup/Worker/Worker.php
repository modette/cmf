<?php declare(strict_types = 1);

namespace Modette\Core\Setup\Worker;

use Modette\Core\Setup\SetupHelper;

interface Worker
{

	public function getName(): string;

	public function work(SetupHelper $helper): void;

}
