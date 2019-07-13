<?php declare(strict_types = 1);

namespace Modette\Http\FrontRouter;

use Contributte\Middlewares\Application\MiddlewareApplication as ApiApplication;
use Nette\DI\Container;

class ApiFrontRouter implements FrontRouter
{

	/** @var string */
	private $apiApplicationName;

	/** @var Container */
	private $container;

	public function __construct(string $apiApplicationName, Container $container)
	{
		$this->apiApplicationName = $apiApplicationName;
		$this->container = $container;
	}

	public function run(): void
	{
		/** @var ApiApplication $application */
		$application = $this->container->getService($this->apiApplicationName);
		$application->run();
	}

}
