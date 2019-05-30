<?php declare(strict_types = 1);

namespace Modette\Core\Boot\Helper;

use Modette\Core\Exception\Logic\InvalidArgumentException;
use Modette\Core\Exception\Logic\InvalidStateException;

class EnvironmentHelper
{

	public static function isEnvironmentDebugMode(string $variableName = 'MODETTE_DEBUG'): bool
	{
		$debug = getenv($variableName);
		return $debug !== false && (strtolower($debug) === 'true' || $debug === '1');
	}

	/**
	 * Collect environment parameters prefixed by $prefix
	 *
	 * @return mixed[]
	 */
	public static function getEnvironmentParameters(string $prefix = 'MODETTE', string $delimiter = '__'): array
	{
		if ($delimiter === '') {
			throw new InvalidArgumentException('Delimiter must be non-empty string');
		}

		$prefix .= $delimiter;

		$map = static function (&$array, array $keys, $value) use (&$map) {
			if (count($keys) <= 0) {
				return $value;
			}

			$key = array_shift($keys);

			if (!is_array($array)) {
				throw new InvalidStateException(sprintf('Invalid structure for key "%s" value "%s"', implode($keys), $value));
			}

			if (!array_key_exists($key, $array)) {
				$array[$key] = [];
			}

			// Recursive
			$array[$key] = $map($array[$key], $keys, $value);

			return $array;
		};

		$parameters = [];
		foreach (getenv() as $key => $value) {
			if (strpos($key, $prefix) === 0) {
				// Parse PREFIX{delimiter=__}{NAME-1}{delimiter=__}{NAME-N}
				$keys = explode($delimiter, strtolower(substr($key, strlen($prefix))));
				// Make array structure
				$map($parameters, $keys, $value);
			}
		}

		return $parameters;
	}

}
