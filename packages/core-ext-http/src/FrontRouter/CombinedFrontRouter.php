<?php declare(strict_types = 1);

namespace Modette\Http\FrontRouter;

use Contributte\Middlewares\Application\MiddlewareApplication as ApiApplication;
use Nette\Application\Application as UIApplication;
use Nette\DI\Container;
use Nette\Http\Request;
use Nette\Utils\Strings;

class CombinedFrontRouter implements FrontRouter
{

	/** @var string */
	private $apiApplicationName;

	/** @var string */
	private $uiApplicationName;

	/** @var Request */
	private $request;

	/** @var Container */
	private $container;

	public function __construct(string $apiApplicationName, string $uiApplicationName, Request $request, Container $container)
	{
		$this->apiApplicationName = $apiApplicationName;
		$this->uiApplicationName = $uiApplicationName;
		$this->request = $request;
		$this->container = $container;
	}

	public function run(): void
	{
		$url = $this->request->getUrl();
		$newPath = substr($url->getPath(), strlen($url->getBasePath()));

		if (Strings::startsWith($newPath, 'api')) {
			$application = $this->container->getService($this->apiApplicationName);
			assert($application instanceof ApiApplication);
			$application->run();
		} else {
			$application = $this->container->getService($this->uiApplicationName);
			assert($application instanceof UIApplication);
			$application->run();
		}
	}

}
