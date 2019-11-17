<?php declare(strict_types = 1);

namespace Modette\Core\Parameters\Utils;

final class ParametersSorter
{

	private const TYPE_ARRAY = 'array';
	private const TYPE_BOOL = 'bool';
	private const TYPE_NUMBER = 'number';
	private const TYPE_STRING = 'string';
	private const TYPE_NULL = 'null';
	private const TYPE_OTHER = 'other';

	/**
	 * @param mixed[] $parameters
	 * @return mixed[]
	 */
	public static function sort(array $parameters): array
	{
		ksort($parameters);
		$byType = [
			self::TYPE_ARRAY => [],
			self::TYPE_BOOL => [],
			self::TYPE_NUMBER => [],
			self::TYPE_STRING => [],
			self::TYPE_NULL => [],
			self::TYPE_OTHER => [],
		];

		foreach ($parameters as $key => $item) {
			if (is_array($item)) {
				$item = self::sort($item);
				$type = self::TYPE_ARRAY;
			} elseif (is_bool($item)) {
				$type = self::TYPE_BOOL;
			} elseif (is_int($item) || is_float($item)) {
				$type = self::TYPE_NUMBER;
			} elseif (is_string($item)) {
				$type = self::TYPE_STRING;
			} elseif ($item === null) {
				$type = self::TYPE_NULL;
			} else {
				$type = self::TYPE_OTHER;
			}

			$byType[$type][$key] = $item;
		}

		$sorted = array_merge(
			$byType[self::TYPE_BOOL],
			$byType[self::TYPE_STRING],
			$byType[self::TYPE_NUMBER],
			$byType[self::TYPE_NULL],
			$byType[self::TYPE_OTHER],
			$byType[self::TYPE_ARRAY]
		);

		return $sorted;
	}

}
