<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

class Plugin implements PluginInterface
{

	public function __construct()
	{
		// Neon::class not exists on initial installation so internal is used
		//if (!class_exists(Neon::class)) {
		//	require __DIR__ . '/../vendor/autoload.php';
		//}
	}

	/**
	 * {@inheritDoc}
	 */
	public function activate(Composer $composer, IOInterface $io): void
	{
		$manager = $composer->getInstallationManager();

		$installer = new Installer($composer, $io);
		$manager->addInstaller($installer);
	}

}
