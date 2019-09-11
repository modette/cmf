<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Command;

use Composer\Command\BaseCommand;
use Modette\ModuleInstaller\Files\File;
use Modette\ModuleInstaller\Files\FileIO;
use Modette\ModuleInstaller\Package\ConfigurationValidator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class ModuleValidateCommand extends BaseCommand
{

	/** @var string */
	protected static $defaultName = 'modette:module:validate';

	protected function configure(): void
	{
		$this->setName(self::$defaultName);
		$this->setDescription(sprintf('Validate %s', File::DEFAULT_NAME));
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$composer = $this->getComposer();
		$validator = new ConfigurationValidator();
		$fileIO = new FileIO();
		$consoleIO = new SymfonyStyle($input, $output);

		$package = $composer->getPackage();
		// Composer supports ProjectInstaller only during create-project command so let's hope no-one change vendor-dir
		$packageDirAbsolute = dirname($composer->getConfig()->get('vendor-dir'));
		$configFile = $packageDirAbsolute . '/' . File::DEFAULT_NAME;

		$validator->validateConfiguration($package->getName(), File::DEFAULT_NAME, $fileIO->read($configFile));

		$consoleIO->success(sprintf('%s successfully validated', File::DEFAULT_NAME));

		return 0;
	}

}
