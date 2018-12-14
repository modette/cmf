<?php declare(strict_types = 1);

namespace Modette\UI\Control\Menu;

use Modette\UI\Menu\Menu;

interface MenuFactory
{

	public function create(Menu $menu): MenuControl;

}
