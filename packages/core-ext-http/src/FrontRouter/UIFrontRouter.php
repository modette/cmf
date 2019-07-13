<?php declare(strict_types = 1);

namespace Modette\Http\FrontRouter;

use Nette\Application\Application as UIApplication;
use Nette\DI\Container;

class UIFrontRouter implements FrontRouter
{

	/** @var string */
	private $uiApplicationName;

	/** @var Container */
	private $container;

	public function __construct(string $uiApplicationName, Container $container)
	{
		$this->uiApplicationName = $uiApplicationName;
		$this->container = $container;
	}

	public function run(): void
	{
		/** @var UIApplication $application */
		$application = $this->container->getService($this->uiApplicationName);
		$application->run();
	}

}
