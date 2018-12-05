<?php declare(strict_types = 1);

namespace Modette\UI\Control\Body;

interface BodyFactory
{

	public function create(): BodyControl;

}
