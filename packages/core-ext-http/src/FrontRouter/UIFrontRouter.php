<?php declare(strict_types = 1);

namespace Modette\Http\FrontRouter;

use Nette\Application\Application as UIApplication;
use Nette\DI\Container;

class UIFrontRouter implements FrontRouter
{

	/** @var Container */
	private $container;

	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	public function run(): void
	{
		/** @var UIApplication $application */
		$application = $this->container->getByType(UIApplication::class, false);
		$application->run();
	}

}
