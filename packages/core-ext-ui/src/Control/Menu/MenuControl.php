<?php declare(strict_types = 1);

namespace Modette\UI\Control\Menu;

use Modette\UI\Base\Control\BaseControl;
use Modette\UI\Menu\Menu;

/**
 * @property-read MenuTemplate $template
 */
class MenuControl extends BaseControl
{

	/** @var Menu */
	private $menu;

	public function __construct(Menu $menu)
	{
		parent::__construct();
		$this->menu = $menu;
	}

	public function render(): void
	{
		$this->template->menu = $this->menu;

		$this->template->setFile(__DIR__ . '/templates/default.latte');
		$this->template->render();
	}

}
