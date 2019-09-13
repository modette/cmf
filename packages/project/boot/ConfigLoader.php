<?php declare(strict_types = 1);

namespace Boot;

use Modette\ModuleInstaller\Loading\Loader;

final class ConfigLoader implements Loader
{

	/**
	 * @return string[]
	 */
	public function getConfigFiles(): array
	{
		return [];
	}

}
