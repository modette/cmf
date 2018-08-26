<?php declare(strict_types = 1);

namespace Modette\Core\DI;

abstract class RootExtension extends PluggableExtension
{

	/** @var mixed[] */
	protected $extensions = [];

}
