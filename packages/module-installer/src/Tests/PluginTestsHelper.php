<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Tests;

use Composer\Composer;
use Composer\Console\Application;
use Composer\Factory;
use Composer\IO\BufferIO;
use Modette\ModuleInstaller\Command\LoaderGenerateCommand;
use Modette\ModuleInstaller\Command\ModuleValidateCommand;
use Modette\ModuleInstaller\Plugin;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;

final class PluginTestsHelper
{

	/**
	 * @return object[] [$plugin, $composer, $io]
	 */
	public static function initializePlugin(): array
	{
		$io = new BufferIO('', OutputInterface::VERBOSITY_VERBOSE);
		$composer = Factory::create($io);
		$plugin = new Plugin();

		$plugin->activate($composer, $io);

		return [$plugin, $composer, $io];
	}

	public static function generateLoader(?string $file = null): int
	{
		[$plugin, $composer, $io] = self::initializePlugin();
		assert($plugin instanceof Plugin);
		assert($composer instanceof Composer);
		assert($io instanceof BufferIO);

		$command = new LoaderGenerateCommand();

		$application = new Application();
		$application->add($command);

		$input = [];

		if ($file !== null) {
			$input['--file'] = $file;
		}

		$tester = new CommandTester($command);
		return $tester->execute($input);
	}

	public static function validateModule(?string $file = null, ?string $package = null): int
	{
		[$plugin, $composer, $io] = self::initializePlugin();
		assert($plugin instanceof Plugin);
		assert($composer instanceof Composer);
		assert($io instanceof BufferIO);

		$command = new ModuleValidateCommand();

		$application = new Application();
		$application->add($command);

		$input = [];

		if ($file !== null) {
			$input['--file'] = $file;
		}

		if ($package !== null) {
			$input['--package'] = $package;
		}

		$tester = new CommandTester($command);
		return $tester->execute($input);
	}

}
