<?php declare(strict_types = 1);

namespace Modette\Core\Utils;

class Booleans
{

	public static function negate(bool $value): bool
	{
		return !$value;
	}

}
