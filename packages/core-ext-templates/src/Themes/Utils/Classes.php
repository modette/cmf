<?php declare(strict_types = 1);

namespace Modette\Templates\Themes\Utils;

use ReflectionClass;

final class Classes
{

	/**
	 * @return string[]
	 */
	public static function getClassList(object $object): array
	{
		$called = get_class($object);
		return [$called] + class_parents($called);
	}

	public static function getClassDir(string $class): string
	{
		$reflection = new ReflectionClass($class);
		return dirname($reflection->getFileName());
	}

}
