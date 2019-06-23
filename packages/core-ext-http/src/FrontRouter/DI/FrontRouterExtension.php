<?php declare(strict_types = 1);

namespace Modette\Http\FrontRouter\DI;

use Modette\Http\FrontRouter\ApiFrontRouter;
use Modette\Http\FrontRouter\CombinedFrontRouter;
use Modette\Http\FrontRouter\FrontRouter;
use Modette\Http\FrontRouter\UIFrontRouter;
use Nette\DI\CompilerExtension;

class FrontRouterExtension extends CompilerExtension
{

	/** @var mixed[] */
	private $defaults = [
		'api' => [
			'enable' => false,
		],
		'ui' => [
			'enable' => false,
		],
	];

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->validateConfig($this->defaults);

		if (!$config['api']['enable'] && !$config['ui']['enable']) {
			return;
		}

		$frontRouterDefinition = $builder->addDefinition($this->prefix('frontRouter'))
			->setType(FrontRouter::class);

		if (!$config['api']['enable']) {
			$frontRouterDefinition->setFactory(UIFrontRouter::class);
		} elseif (!$config['ui']['enable']) {
			$frontRouterDefinition->setFactory(ApiFrontRouter::class);
		} else {
			$frontRouterDefinition->setFactory(CombinedFrontRouter::class);
		}
	}

}
