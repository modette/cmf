<?php declare(strict_types = 1);

namespace Modette\Templates\Themes\ComponentTypes;

use Nette\Application\UI\Presenter;

class PresenterTypeChecker implements ComponentTypeChecker
{

	public const TYPE = 'presenter';

	public function check(string $class): bool
	{
		return is_subclass_of($class, Presenter::class);
	}

}
