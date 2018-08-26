<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Installer\PackageEvent;
use Composer\Installer\PackageEvents;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;
use RuntimeException;

/**
 * @todo - nabídnout z modette.neon minimální konfiguraci? (viz contributte/neonizer)
 *       - ta musí být pro konkrétní modul, kolize se neřeší
 *       - konfigurace se vkládá do aplikačního config/modules/(api|front|admin|core).neon?
 */
class Plugin implements PluginInterface, EventSubscriberInterface
{

	/**
	 * {@inheritDoc}
	 */
	public function activate(Composer $composer, IOInterface $io): void
	{
		// Must be implemented
	}

	/**
	 * @return string[]
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			//ScriptEvents::POST_INSTALL_CMD => 'configure',
			ScriptEvents::POST_UPDATE_CMD => 'configure',
			PackageEvents::PRE_PACKAGE_UNINSTALL => 'remove',
		];
	}

	public function configure(Event $event): void
	{
		$composer = $event->getComposer();
		if (!$this->isEnabled($composer)) {
			return;
		}

		$configFile = $this->loadMainConfig($composer);

		$installationManager = $composer->getInstallationManager();
		$localRepository = $composer->getRepositoryManager()->getLocalRepository();
		$packages = $localRepository->getCanonicalPackages();
		$excluded = $composer->getPackage()->getExtra()['modette']['excluded'] ?? [];

		// Filter out ignored packages and packages without modette.neon
		foreach ($packages as $key => $package) {
			// Package ignored by config
			if (in_array($package->getName(), $excluded, true)) {
				unset($packages[$key]);
			}

			// Ignore packages without modette.neon
			$packageDir = $installationManager->getInstallPath($package);
			if (!file_exists($packageDir . '/modette.neon')) {
				unset($packages[$key]);
			}
		}

		//TODO - load modules in order based on composer dependency graph (if it's not done by composer itself already)
		// composer show --tree
		// https://github.com/schmittjoh/composer-deps-analyzer
		// https://github.com/bulton-fr/dependency-tree
		$rootDir = $this->getRootDir($composer);
		$modulesConfig = [];
		foreach ($packages as $package) {
			$packageDirAbsolute = $installationManager->getInstallPath($package);
			$moduleConfigFile = $packageDirAbsolute . '/modette.neon';
			$packageDirRelative = substr($packageDirAbsolute, strlen($rootDir));
			$modulesConfig = array_merge(
				$modulesConfig,
				$this->loadModuleConfig($moduleConfigFile, $packageDirRelative, $rootDir)
			);
		}

		$configurator = new Config();
		$configurator->write($configFile, $modulesConfig);
	}

	public function remove(PackageEvent $event): void
	{
		$composer = $event->getComposer();
		if (!$this->isEnabled($composer)) {
			return;
		}

		$configFile = $this->loadMainConfig($composer);
		$configurator = new Config();
		$modulesConfig = $configurator->load($configFile);

		//TODO - smazat všechny configy uvedené v modette.neon mazaného package
		//TODO - jak zjistit, o který package jde? musím použít installer?

		$configurator->write($configFile, $modulesConfig);
	}

	private function isEnabled(Composer $composer): bool
	{
		$extra = $composer->getPackage()->getExtra();
		$pluginConfig = $extra['modette'] ?? [];

		$enabled = $pluginConfig['enable'] ?? false;
		return (bool) $enabled;
	}

	/**
	 * @return string[]
	 */
	private function loadModuleConfig(string $file, string $packageDir, string $rootDir): array
	{
		if (!file_exists($file)) {
			throw new RuntimeException(sprintf(
				'%s does not exist.',
				$file
			));
		}

		$configurator = new Config();
		$content = $configurator->load($file);

		// Make absolute path from relative path (prepend relative package path)
		// config/config.neon -> vendor/foo/bar/config/config.neon
		$configs = $content['configs'] ?? [];
		foreach ($configs as $key => $config) {
			$configs[$key] = $packageDir . '/' . $config;
		}

		// Load included module configs (useful for monolithic repositories)
		$includes = $content['includes'] ?? [];
		foreach ($includes as $include) {
			$configs = array_merge(
				$configs,
				$this->loadModuleConfig(
					$rootDir . '/' . $packageDir . '/' . $include,
					dirname($packageDir . '/' . $include),
					$rootDir
				)
			);
		}

		return $configs;
	}

	private function getRootDir(Composer $composer): string
	{
		$vendorDir = $composer->getConfig()->get('vendor-dir');
		return dirname($vendorDir);
	}

	private function loadMainConfig(Composer $composer): string
	{
		$extra = $composer->getPackage()->getExtra();
		$pluginConfig = $extra['modette'] ?? [];

		$rootDir = $this->getRootDir($composer);
		$configFile = isset($pluginConfig['modules']) ?
			$rootDir . '/' . $pluginConfig['modules'] :
			$rootDir . '/config/modules.neon';

		if (!file_exists($configFile)) {
			throw new RuntimeException(sprintf(
				'%s does not exist. Is key extra.modette.modules in your composer.json properly configured?',
				$configFile
			));
		}

		return $configFile;
	}

}
