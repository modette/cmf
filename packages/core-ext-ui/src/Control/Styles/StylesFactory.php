<?php declare(strict_types = 1);

namespace Modette\UI\Control\Styles;

interface StylesFactory
{

	public function create(): StylesControl;

}
