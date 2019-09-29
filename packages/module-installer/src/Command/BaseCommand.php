<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Command;

use Composer\Command\BaseCommand as ComposerBaseCommand;
use Modette\ModuleInstaller\Files\File;
use Symfony\Component\Console\Input\InputOption;

abstract class BaseCommand extends ComposerBaseCommand
{

	protected const OPTION_FILE = 'file';

	protected function configure(): void
	{
		$this->addOption(
			self::OPTION_FILE,
			'f',
			InputOption::VALUE_REQUIRED,
			sprintf('Use different config file than %s (for tests)', File::DEFAULT_NAME),
			File::DEFAULT_NAME
		);
	}

}
