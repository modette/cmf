<?php declare(strict_types = 1);

namespace Modette\Http\FrontRouter;

use Contributte\Middlewares\Application\MiddlewareApplication as ApiApplication;
use Nette\Application\Application as UIApplication;
use Nette\DI\Container;
use Nette\Http\Request;
use Nette\Utils\Strings;

class CombinedFrontRouter implements FrontRouter
{

	/** @var Request */
	private $request;

	/** @var Container */
	private $container;

	public function __construct(Request $request, Container $container)
	{
		$this->request = $request;
		$this->container = $container;
	}

	public function run(): void
	{
		$url = $this->request->getUrl();
		$newPath = substr($url->getPath(), strlen($url->getBasePath()));

		if (Strings::startsWith($newPath, 'api')) {
			/** @var ApiApplication $application */
			$application = $this->container->getByType(ApiApplication::class, false);
			$application->run();
		} else {
			/** @var UIApplication $application */
			$application = $this->container->getByType(UIApplication::class, false);
			$application->run();
		}
	}

}
