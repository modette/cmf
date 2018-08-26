<?php declare(strict_types = 1);

namespace Modette\UI\Controls\Body;

interface BodyFactory
{

	public function create(): BodyControl;

}
