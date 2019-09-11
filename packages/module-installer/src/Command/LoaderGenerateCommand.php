<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Command;

use Composer\Command\BaseCommand;
use Modette\ModuleInstaller\Loading\LoaderGenerator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class LoaderGenerateCommand extends BaseCommand
{

	/** @var string */
	protected static $defaultName = 'modette:loader:generate';

	protected function configure(): void
	{
		$this->setName(self::$defaultName);
		$this->setDescription('Generate modules loader');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$loaderGenerator = new LoaderGenerator();
		$loaderGenerator->generateLoader($this->getComposer());
		$consoleIO = new SymfonyStyle($input, $output);

		$consoleIO->success('Modules loader successfully generated');

		return 0;
	}

}
