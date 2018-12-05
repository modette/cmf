<?php declare(strict_types = 1);

namespace Modette\UI\Control\Head;

interface HeadFactory
{

	public function create(): HeadControl;

}
