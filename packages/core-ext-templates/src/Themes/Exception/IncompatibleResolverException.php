<?php declare(strict_types = 1);

namespace Modette\Templates\Themes\Exception;

use Modette\Exceptions\DomainException;

final class IncompatibleResolverException extends DomainException
{

	public function __construct()
	{
		parent::__construct('');
	}

}
