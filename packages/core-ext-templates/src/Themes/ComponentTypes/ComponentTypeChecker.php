<?php declare(strict_types = 1);

namespace Modette\Templates\Themes\ComponentTypes;

interface ComponentTypeChecker
{

	public function check(string $class): bool; // TODO - lepší název

}
