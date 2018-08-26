<?php declare(strict_types = 1);

namespace Modette\UI\DI;

use Nette\Application\InvalidPresenterException;
use Nette\Application\IPresenter;
use Nette\DI\Container;

class PresenterFactoryCallback
{

	/** @var Container */
	private $container;

	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	public function __invoke(string $class): IPresenter
	{
		$services = array_keys($this->container->findByTag('nette.presenter'), $class, true);

		if ($services === []) {
			throw new InvalidPresenterException(sprintf(
				'Presenter "%s" is not registered as a service.',
				$class
			));
		}

		if (count($services) !== 1) {
			throw new InvalidPresenterException(sprintf(
				'Multiple services of type "%s" found: %s.',
				$class,
				implode(', ', $services)
			));
		}

		/** @var IPresenter $service */
		$service = $this->container->createService($services[0]);
		return $service;
	}

}
