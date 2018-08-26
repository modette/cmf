<?php declare(strict_types = 1);

namespace Modette\Core\Setup\Driver;

class CacheCleanDriver implements Driver
{

	public function upgrade(): void
	{
		//Smazat cache překladů (aby se načetly nové překlady)
	}

	public function reload(): void
	{
		//contributte:cache:clean
	}

}
