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
use LogicException;
use RuntimeException;
use UnexpectedValueException;

/**
 * @todo - nabídnout z modette.neon minimální konfiguraci? (viz contributte/neonizer)
 *       - ta musí být pro konkrétní modul, kolize se neřeší
 *       - konfigurace se vkládá do aplikačního config/modules/(api|front|admin|core).neon?
 */
class Plugin implements PluginInterface, EventSubscriberInterface
{

	/**
	 * @return string[]
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			ScriptEvents::POST_INSTALL_CMD => 'install',
			ScriptEvents::POST_UPDATE_CMD => 'update',
			PackageEvents::POST_PACKAGE_UNINSTALL => 'remove',
		];
	}

	/**
	 * {@inheritDoc}
	 */
	public function activate(Composer $composer, IOInterface $io): void
	{
		// Must be implemented
	}

	public function install(Event $event): void
	{
		$composer = $event->getComposer();
		$this->generateRootConfig($composer);
	}

	public function update(Event $event): void
	{
		$composer = $event->getComposer();
		$this->generateRootConfig($composer);
	}

	public function remove(PackageEvent $event): void
	{
		$composer = $event->getComposer();
		$this->generateRootConfig($composer);
	}

	private function generateRootConfig(Composer $composer): void
	{
		if (!$this->isEnabled($composer)) {
			return;
		}

		$rootConfigFile = $this->getRootConfigFile($composer);

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

		$configurator = new IO();
		$configurator->write($rootConfigFile, $modulesConfig);
	}

	/**
	 * @return mixed[]
	 */
	private function loadModuleConfig(string $file, string $packageDir, string $rootDir): array
	{
		if (!file_exists($file)) {
			throw new RuntimeException(sprintf(
				'Config file %s does not exist.',
				$file
			));
		}

		$io = new IO();
		$content = $io->read($file);

		// Prepend relative package path
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

	private function getRootConfigFile(Composer $composer): string
	{
		$extra = $composer->getPackage()->getExtra();
		$pluginConfig = $extra['modette'] ?? [];

		if (!isset($pluginConfig['modules'])) {
			throw new LogicException('composer.json key extra.modette.modules must be defined when modette/module-installer is enabled.');
		}

		$configFileRelative = $pluginConfig['modules'];
		$configFile = $this->getRootDir($composer) . '/' . $configFileRelative;

		if (!file_exists($configFile)) {
			throw new UnexpectedValueException(sprintf(
				'composer.json key extra.modette.modules must be a valid relative path to a config file. Given path is %s, which resolved into %s',
				$configFileRelative,
				$configFile
			));
		}

		return $configFile;
	}

	private function getRootDir(Composer $composer): string
	{
		$vendorDir = $composer->getConfig()->get('vendor-dir');
		return dirname($vendorDir);
	}

	private function isEnabled(Composer $composer): bool
	{
		$extra = $composer->getPackage()->getExtra();
		$pluginConfig = $extra['modette'] ?? [];

		$enabled = $pluginConfig['enable'] ?? false;

		if (!is_bool($enabled)) {
			throw new UnexpectedValueException('composer.json key extra.modette.enable must be boolean.');
		}

		return $enabled;
	}

}
