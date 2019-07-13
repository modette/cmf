<?php declare(strict_types = 1);

namespace Modette\Http\FrontRouter\DI;

use Contributte\Middlewares\Application\MiddlewareApplication as ApiApplication;
use Modette\Http\FrontRouter\ApiFrontRouter;
use Modette\Http\FrontRouter\CombinedFrontRouter;
use Modette\Http\FrontRouter\FrontRouter;
use Modette\Http\FrontRouter\UIFrontRouter;
use Nette\Application\Application as UIApplication;
use Nette\DI\CompilerExtension;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use stdClass;

/**
 * @property-read stdClass $config
 */
class FrontRouterExtension extends CompilerExtension
{

	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'api' => Expect::structure([
				'enable' => Expect::bool(false),
			]),
			'ui' => Expect::structure([
				'enable' => Expect::bool(false),
			]),
		]);
	}

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->config;

		if (!$config->api->enable && !$config->ui->enable) {
			return;
		}

		$frontRouterDefinition = $builder->addDefinition($this->prefix('frontRouter'))
			->setType(FrontRouter::class);

		if (!$config->api->enable) {
			$frontRouterDefinition->setFactory(UIFrontRouter::class, [
				$builder->getByType(UIApplication::class),
			]);
		} elseif (!$config->ui->enable) {
			$frontRouterDefinition->setFactory(ApiFrontRouter::class, [
				$builder->getByType(ApiApplication::class),
			]);
		} else {
			$frontRouterDefinition->setFactory(CombinedFrontRouter::class, [
				$builder->getByType(ApiApplication::class),
				$builder->getByType(UIApplication::class),
			]);
		}
	}

}
