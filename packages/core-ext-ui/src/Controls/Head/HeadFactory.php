<?php declare(strict_types = 1);

namespace Modette\UI\Controls\Head;

interface HeadFactory
{

	public function create(): HeadControl;

}
