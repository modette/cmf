<?php declare(strict_types = 1);

namespace Modette\Http\FrontRouter;

use Contributte\Middlewares\Application\MiddlewareApplication as ApiApplication;
use Nette\DI\Container;

class ApiFrontRouter implements FrontRouter
{

	/** @var Container */
	private $container;

	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	public function run(): void
	{
		/** @var ApiApplication $application */
		$application = $this->container->getByType(ApiApplication::class, false);
		$application->run();
	}

}
