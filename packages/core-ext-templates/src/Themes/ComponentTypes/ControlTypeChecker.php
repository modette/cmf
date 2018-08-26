<?php declare(strict_types = 1);

namespace Modette\Templates\Themes\ComponentTypes;

class ControlTypeChecker implements ComponentTypeChecker
{

	public const TYPE = 'control';

	public function check(string $class): bool
	{
		//return is_subclass_of(Control::class);
		return false;
	}

}
