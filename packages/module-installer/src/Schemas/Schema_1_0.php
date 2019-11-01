<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Schemas;

use Modette\ModuleInstaller\Configuration\FileConfiguration;
use Modette\ModuleInstaller\Configuration\LoaderConfiguration;
use Modette\ModuleInstaller\Configuration\PackageConfiguration;
use Modette\ModuleInstaller\Configuration\SimulatedModuleConfiguration;
use Nette\Schema\Elements\Structure;
use Nette\Schema\Expect;

final class Schema_1_0 implements Schema
{

	public function getStructure(): Structure
	{
		return Expect::structure([
			PackageConfiguration::VERSION_OPTION => Expect::anyOf(self::VERSION_1_0),
			PackageConfiguration::LOADER_OPTION => Expect::anyOf(
				Expect::null(),
				Expect::structure([
					LoaderConfiguration::FILE_OPTION => Expect::string()->required(),
					LoaderConfiguration::CLASS_OPTION => Expect::string()->required(),
				])->castTo('array')
			),
			PackageConfiguration::FILES_OPTION => Expect::listOf(Expect::anyOf(
				Expect::string(),
				Expect::structure([
					FileConfiguration::FILE_OPTION => Expect::string()->required(),
					FileConfiguration::PARAMETERS_OPTION => Expect::arrayOf(
						Expect::anyOf(Expect::array(), Expect::scalar(), Expect::null())
					),
					FileConfiguration::PACKAGES_OPTION => Expect::listOf(
						Expect::string()
					),
				])->castTo('array')
			)),
			PackageConfiguration::IGNORE_OPTION => Expect::listOf(
				Expect::string()
			),
			PackageConfiguration::SIMULATED_MODULES_OPTION => Expect::arrayOf(Expect::anyOf(
				Expect::string(),
				Expect::structure([
					SimulatedModuleConfiguration::PATH_OPTION => Expect::string()->required(),
					SimulatedModuleConfiguration::OPTIONAL_OPTION => Expect::bool(SimulatedModuleConfiguration::OPTIONAL_DEFAULT),
				])->castTo('array')
			)),
		])->castTo('array');
	}

}
