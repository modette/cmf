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
			'loader' => Expect::anyOf(
				Expect::null(),
				Expect::structure([
					'file' => Expect::string()->required(),
					'class' => Expect::string()->required(),
				])->castTo('array')
			),
			'files' => Expect::arrayOf(Expect::anyOf(
				Expect::string(),
				Expect::structure([
					'file' => Expect::string()->required(),
					'parameters' => Expect::arrayOf(
						Expect::anyOf(Expect::array(), Expect::scalar(), Expect::null())
					),
				])->castTo('array')
			)),
			'ignored' => Expect::arrayOf(
				Expect::string()
			),
		])->castTo('array');
	}

}
