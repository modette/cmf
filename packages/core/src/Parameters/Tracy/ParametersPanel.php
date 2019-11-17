<?php declare(strict_types = 1);

namespace Modette\Core\Parameters\Tracy;

use Modette\Core\Parameters\Utils\ParametersSorter;
use Nette\DI\Container;
use Tracy\IBarPanel;

final class ParametersPanel implements IBarPanel
{

	/** @var Container */
	private $container;

	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	public function getTab(): string
	{
		ob_start();
		require __DIR__ . '/templates/tab.phtml';
		return (string) ob_get_clean();
	}

	public function getPanel(): string
	{
		$parameters = ParametersSorter::sort($this->container->getParameters());
		ob_start();
		require __DIR__ . '/templates/panel.phtml';
		return (string) ob_get_clean();
	}

}
