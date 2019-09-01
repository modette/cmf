<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Schemas;

use Nette\Schema\Elements\Structure;
use Nette\Schema\Expect;

final class Schema_1_0 implements Schema
{

	public function getStructure(): Structure
	{
		return Expect::structure([
			'version' => Expect::anyOf(self::VERSION_1_0),
			'files' => Expect::arrayOf(Expect::anyOf(
				Expect::string(),
				Expect::structure([
					'file' => Expect::string()->required(),
				])->castTo('array')
			)),
		])->castTo('array');
	}

}
