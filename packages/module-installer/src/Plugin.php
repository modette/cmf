<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Installer\PackageEvent;
use Composer\Installer\PackageEvents;
use Composer\IO\IOInterface;
use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;
use Composer\Plugin\Capable;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;
use Modette\ModuleInstaller\Command\CommandProvider;
use Modette\ModuleInstaller\Loading\LoaderGenerator;

final class Plugin implements PluginInterface, EventSubscriberInterface, Capable
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
	 * @return string[]
	 */
	public function getCapabilities(): array
	{
		return [
			CommandProviderCapability::class => CommandProvider::class,
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
		$loaderGenerator = new LoaderGenerator();
		$loaderGenerator->generateLoader($event->getComposer());
	}

	public function update(Event $event): void
	{
		$loaderGenerator = new LoaderGenerator();
		$loaderGenerator->generateLoader($event->getComposer());
	}

	public function remove(PackageEvent $event): void
	{
		$loaderGenerator = new LoaderGenerator();
		$loaderGenerator->generateLoader($event->getComposer());
	}

}
