<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Command;

use Composer\Command\BaseCommand;
use Exception;
use Modette\ModuleInstaller\Files\File;
use Modette\ModuleInstaller\Loading\LoaderGenerator;
use Modette\ModuleInstaller\Utils\PluginActivator;
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
		$composer = $this->getComposer();

		if (!PluginActivator::isEnabled($composer)) {
			throw new Exception(sprintf(
				'Cannot generate module loader, \'%s\' with \'loader\' option must be configured.',
				File::DEFAULT_NAME
			));
		}

		$io = new SymfonyStyle($input, $output);
		$loaderGenerator = new LoaderGenerator($composer);

		$loaderGenerator->generateLoader();
		$io->success('Modules loader successfully generated');

		return 0;
	}

}
