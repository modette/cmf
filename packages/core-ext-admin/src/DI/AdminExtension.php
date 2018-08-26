<?php declare(strict_types = 1);

namespace Modette\Admin\DI;

use Modette\Core\Exception\Logic\InvalidStateException;
use Nette\DI\CompilerExtension;

class AdminExtension extends CompilerExtension
{

	/** @var mixed[] */
	protected $defaults = [
		'sign' => [
			'signInAction' => null,
			'signOutAction' => null,
		],
	];

	public function loadConfiguration(): void
	{
		parent::loadConfiguration();

		$config = $this->validateConfig($this->defaults);
		$builder = $this->getContainerBuilder();

		if ($config['sign']['signInAction'] === null || $config['sign']['signOutAction'] === null) {
			throw new InvalidStateException(sprintf(
				'Provide %s and %s.',
				$this->prefix('sign.signInAction'),
				$this->prefix('sign.signOutAction')
			));
		}

		$builder->addDefinition($this->prefix('config'))
			->setFactory(AdminConfig::class, [
				$config['sign']['signInAction'],
				$config['sign']['signOutAction'],
			]);
	}

}
