<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Command;

use Modette\Exceptions\Logic\InvalidStateException;
use Modette\ModuleInstaller\Files\FileIO;
use Modette\ModuleInstaller\Loading\LoaderGenerator;
use Modette\ModuleInstaller\Package\ConfigurationValidator;
use Modette\ModuleInstaller\Utils\PathResolver;
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
		parent::configure();

		$this->setName(self::$defaultName);
		$this->setDescription('Generate modules loader');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$composer = $this->getComposer();

		$fileName = $input->getOption(self::OPTION_FILE);
		assert(is_string($fileName));

		$pathResolver = new PathResolver($composer);
		$fileIo = new FileIO();
		$validator = new ConfigurationValidator($fileIo, $pathResolver);
		$activator = new PluginActivator(
			$composer->getPackage(),
			$validator,
			$pathResolver,
			$fileName
		);

		if (!$activator->isEnabled()) {
			throw new InvalidStateException(sprintf(
				'Cannot generate module loader, \'%s\' with \'loader\' option must be configured.',
				$fileName
			));
		}

		$io = new SymfonyStyle($input, $output);
		$loaderGenerator = new LoaderGenerator($composer, $fileIo, $pathResolver, $validator, $activator->getRootPackageConfiguration());

		$loaderGenerator->generateLoader();
		$io->success('Modules loader successfully generated');

		return 0;
	}

}
