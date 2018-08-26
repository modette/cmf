<?php declare(strict_types = 1);

namespace Modette\Core\Utils;

use Nette\Utils\Strings as NetteStrings;

class Strings extends NetteStrings
{

	/**
	 * Remove white characters from the beginning and end of a string
	 * and convert multiple spaces between characters to one space
	 */
	public static function spaceless(string $string): string
	{
		$string = self::trim($string);
		$string = self::replace($string, '#\s{2,}#', ' ');
		return $string;
	}

}
