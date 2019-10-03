<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Command;

use Composer\Semver\Constraint\EmptyConstraint;
use LogicException;
use Modette\ModuleInstaller\Files\FileIO;
use Modette\ModuleInstaller\Package\ConfigurationValidator;
use Modette\ModuleInstaller\Plugin;
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
		parent::configure();

		$this->setName(self::$defaultName);
		$this->setDescription(sprintf('Validate %s', Plugin::DEFAULT_FILE_NAME));

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

		$fileName = $input->getOption(self::OPTION_FILE);
		assert(is_string($fileName));

		$pathResolver = new PathResolver($composer);
		$validator = new ConfigurationValidator(new FileIO(), $pathResolver);
		$io = new SymfonyStyle($input, $output);

		if (($packageName = $input->getOption(self::OPTION_PACKAGE)) !== null) {
			assert(is_string($packageName));
			$package = $composer->getRepositoryManager()->getLocalRepository()->findPackage($packageName, new EmptyConstraint());

			if ($package === null) {
				throw new LogicException(sprintf('Package \'%s\' does not exists', $packageName));
			}
		} else {
			$package = $composer->getPackage();
		}

		$validator->validateConfiguration($package, $fileName);
		$io->success(sprintf('%s successfully validated', $fileName));

		return 0;
	}

}
