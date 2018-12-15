<?php declare(strict_types = 1);

namespace Modette\UI\Routing;

use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;

class Router
{

	/** @var RouteList */
	private $routeList;

	public function __construct(RouteList $routeList)
	{
		$this->routeList = $routeList;
	}

	public function addStyle(string $name, $style): void
	{
		Route::$styles[$name] = $style;
	}

	public function create(): RouteList
	{
		return $this->routeList;
	}

}
