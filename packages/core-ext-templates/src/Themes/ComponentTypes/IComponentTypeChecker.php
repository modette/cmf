<?php declare(strict_types = 1);

namespace Modette\Themes\ComponentTypes;

interface IComponentTypeChecker
{

	public function check(string $class): bool; // TODO - lepší název

}
