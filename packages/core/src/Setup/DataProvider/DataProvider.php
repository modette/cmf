<?php declare(strict_types = 1);

namespace Modette\Core\Setup\DataProvider;

use Modette\Core\Setup\SetupHelper;

interface DataProvider
{

	public function run(SetupHelper $setupHelper): void;

}
