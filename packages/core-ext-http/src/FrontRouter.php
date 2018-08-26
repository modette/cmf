<?php declare(strict_types = 1);

namespace Modette\Http;

use Nette\Application\Application;
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

	public function getApplication(): object
	{
		//todo - ani jedna z applications nemusí existovat
		$url = $this->request->getUrl();
		$newPath = substr($url->getPath(), strlen($url->getBasePath()));
		if (Strings::startsWith($newPath, 'api')) {
			// TODO - middleware application
			return $this->container->getByType(Application::class);
		} else {
			return $this->container->getByType(Application::class);
		}
	}

}
