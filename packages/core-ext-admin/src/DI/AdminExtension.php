<?php declare(strict_types = 1);

namespace Modette\Admin\DI;

use Nette\DI\CompilerExtension;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use stdClass;

/**
 * @property-read stdClass $config
 */
class AdminExtension extends CompilerExtension
{

	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'sign' => Expect::structure([
				'signInAction' => Expect::string()->required(),
				'signOutAction' => Expect::string()->required(),
			]),
		]);
	}

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->config;

		$builder->addDefinition($this->prefix('config'))
			->setFactory(AdminConfig::class, [
				$config->sign->signInAction,
				$config->sign->signOutAction,
			]);
	}

}
