<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Command;

use Composer\Command\BaseCommand;
use Composer\Semver\Constraint\EmptyConstraint;
use LogicException;
use Modette\ModuleInstaller\Files\File;
use Modette\ModuleInstaller\Files\FileIO;
use Modette\ModuleInstaller\Package\ConfigurationValidator;
use Modette\ModuleInstaller\Utils\PathResolver;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class ModuleValidateCommand extends BaseCommand
{

	private const OPTION_PACKAGE = 'package';

	/** @var string */
	protected static $defaultName = 'modette:module:validate';

	protected function configure(): void
	{
		$this->setName(self::$defaultName);
		$this->setDescription(sprintf('Validate %s', File::DEFAULT_NAME));

		$this->addOption(
			self::OPTION_PACKAGE,
			'p',
			InputOption::VALUE_REQUIRED,
			'Package which is validated (current package is validated if not specified)'
		);
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$composer = $this->getComposer();
		$validator = new ConfigurationValidator();
		$fileIO = new FileIO();
		$consoleIO = new SymfonyStyle($input, $output);

		if (($packageName = $input->getOption(self::OPTION_PACKAGE)) !== null) {
			assert(is_string($packageName));
			$package = $composer->getRepositoryManager()->getLocalRepository()->findPackage($packageName, new EmptyConstraint());

			if ($package === null) {
				throw new LogicException(sprintf('Package \'%s\' does not exists', $packageName));
			}
		} else {
			$package = $composer->getPackage();
		}

		$pathResolver = new PathResolver($composer);
		$configFile = $pathResolver->getConfigFileFqn($package);
		$validator->validateConfiguration($package->getName(), File::DEFAULT_NAME, $fileIO->read($configFile));

		$consoleIO->success(sprintf('%s successfully validated', File::DEFAULT_NAME));

		return 0;
	}

}
