<?php declare(strict_types = 1);

namespace Modette\Core\Boot\Helper;

class HttpHelper
{

	/**
	 * @param string[] $cookieList
	 */
	public static function hasDebugCookie(array $cookieList = [], string $cookieName = 'modette-debug'): bool
	{
		$cookie = is_string($_COOKIE[$cookieName] ?? null)
			? $_COOKIE[$cookieName]
			: null;

		if ($cookie === null) {
			return false;
		}

		return in_array($cookie, $cookieList, true);
	}

	public static function isLocalhost(): bool
	{
		$list = [];

		if (!isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !isset($_SERVER['HTTP_FORWARDED'])) { // Forwarded for BC, X-Forwarded-For is standard
			$list[] = '127.0.0.1';
			$list[] = '::1';
		}

		$address = $_SERVER['REMOTE_ADDR'] ?? php_uname('n');

		return in_array($address, $list, true);
	}

}
