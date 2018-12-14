<?php declare(strict_types = 1);

namespace Modette\Http;

use Contributte\Middlewares\Application\MiddlewareApplication;
use Modette\Core\Exception\Logic\InvalidStateException;
use Nette\Application\Application as NetteApplication;
use Nette\DI\Container;
use Nette\Http\Request;
use Nette\Utils\Strings;

class FrontRouter
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

	/**
	 * @return MiddlewareApplication|NetteApplication
	 */
	public function getApplication(): object
	{
		$url = $this->request->getUrl();
		$newPath = substr($url->getPath(), strlen($url->getBasePath()));

		if (Strings::startsWith($newPath, 'api')) {
			// Try get application for api, ui application otherwise
			$application = $this->container->getByType(MiddlewareApplication::class, false);
			if ($application === null) $application = $this->container->getByType(NetteApplication::class, false);
			if ($application === null) throw new InvalidStateException('Install "modette/core-ext-api" or "modette/core-ext-ui" to use FrontRouter.');
		} else {
			// Try get application for ui, api application otherwise
			$application = $this->container->getByType(NetteApplication::class, false);
			if ($application === null) $application = $this->container->getByType(MiddlewareApplication::class, false);
			if ($application === null) throw new InvalidStateException('Install "modette/core-ext-api" or "modette/core-ext-ui" to use FrontRouter.');
		}

		return $application;
	}

}
