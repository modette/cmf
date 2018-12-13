<?php declare(strict_types = 1);

namespace Modette\Core\Boot\Helper;

class HttpHelper
{

	public const COOKIE_SECRET = 'nette-debug';

	/**
	 * @param string[] $cookieList
	 */
	public static function hasDebugCookie(array $cookieList = []): bool
	{
		$cookie = is_string($_COOKIE[self::COOKIE_SECRET] ?? null)
			? $_COOKIE[self::COOKIE_SECRET]
			: null;

		if ($cookie === null) {
			return false;
		}

		if (in_array($cookie, $cookieList, true)) {
			return true;
		}

		return false;
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
