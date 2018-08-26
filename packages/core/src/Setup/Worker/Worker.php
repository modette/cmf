<?php declare(strict_types = 1);

namespace Modette\Core\Setup\Worker;

use Modette\Core\Setup\SetupMeta;

interface Worker
{

	public function work(SetupMeta $meta): void;

}
