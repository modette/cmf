<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Loading;

interface Loader
{

	/**
	 * @return string[]
	 */
	public function getConfigFiles(): array;

}
