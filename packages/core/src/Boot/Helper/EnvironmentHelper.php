<?php declare(strict_types = 1);

namespace Modette\Core\Boot\Helper;

use Modette\Core\Exception\Logic\InvalidStateException;

class EnvironmentHelper
{

	public static function isEnvironmentDebugMode(): bool
	{
		$debug = getenv('NETTE_DEBUG');
		return $debug !== false && (strtolower($debug) === 'true' || $debug === '1');
	}

	/**
	 * Collect environment parameters with NETTE__ prefix
	 *
	 * @return mixed[]
	 */
	public static function getEnvironmentParameters(): array
	{
		$map = function (&$array, array $keys, $value) use (&$map) {
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
		foreach ($_SERVER as $key => $value) {
			// Ensure value
			$value = getenv($key);
			if ($value !== false && strpos($key, 'NETTE__') === 0) {
				// Parse NETTE__{NAME-1}__{NAME-N}
				$keys = explode('__', strtolower(substr($key, 7)));
				// Make array structure
				$map($parameters, $keys, $value);
			}
		}

		return $parameters;
	}

}
