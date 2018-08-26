<?php declare(strict_types = 1);

namespace Modette\UI\Controls\Styles;

interface StylesFactory
{

	public function create(): StylesControl;

}
